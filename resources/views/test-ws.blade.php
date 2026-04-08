@extends('layouts.app')

@section('content')
<div class="test-ws-container">
    <div class="header">
        <h1>WebSocket Random Stream</h1>
        <p class="subtitle">Live data stream from LibxaFrame WebSocket Server</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card neon-blue">
            <div class="stat-label">Random Number</div>
            <div id="random-value" class="stat-value">--</div>
            <div class="stat-suffix">Real-time Pulse</div>
        </div>
        <div class="stat-card neon-purple">
            <div class="stat-label">Last Updated</div>
            <div id="last-updated" class="stat-value">--:--:--</div>
            <div class="stat-suffix">Server Time</div>
        </div>
        <div class="stat-card neon-green">
            <div class="stat-label">Connection</div>
            <div id="connection-status" class="stat-value status-offline">Offline</div>
            <div class="stat-suffix" id="ping-stats">Latency: --ms</div>
        </div>
    </div>

    <div class="log-container">
        <div class="log-header">
            <span>Event Log</span>
            <button id="clear-log">Clear</button>
        </div>
        <div id="event-log" class="log-content">
            <div class="log-entry system">Waiting for connection...</div>
        </div>
    </div>
</div>

<style>
.test-ws-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1.5rem;
    font-family: 'Inter', sans-serif;
}

.header {
    text-align: center;
    margin-bottom: 3rem;
}

.header h1 {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: var(--color-text-secondary);
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--color-background-secondary);
    border: 1px solid var(--color-border-primary);
    border-radius: 1.25rem;
    padding: 2rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--card-color);
    box-shadow: 0 10px 30px -10px var(--card-glow);
}

.neon-blue { --card-color: #3b82f6; --card-glow: rgba(59, 130, 246, 0.3); }
.neon-purple { --card-color: #a855f7; --card-glow: rgba(168, 85, 247, 0.3); }
.neon-green { --card-color: #10b981; --card-glow: rgba(16, 185, 129, 0.3); }

.stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--color-text-secondary);
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 3rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.5rem;
}

.stat-suffix {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
    opacity: 0.7;
}

.status-offline { color: #ef4444; }
.status-online { color: #10b981; }

.log-container {
    background: #0f172a;
    border-radius: 1rem;
    overflow: hidden;
    border: 1px solid #1e293b;
}

.log-header {
    background: #1e293b;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #f8fafc;
    font-weight: 600;
}

.log-header button {
    background: transparent;
    border: 1px solid #334155;
    color: #94a3b8;
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.2s;
}

.log-header button:hover {
    background: #334155;
    color: #f8fafc;
}

.log-content {
    height: 300px;
    overflow-y: auto;
    padding: 1rem;
    font-family: 'Fira Code', monospace;
    font-size: 0.875rem;
}

.log-entry {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
    border-left: 2px solid transparent;
}

.log-entry.system { color: #94a3b8; border-color: #475569; }
.log-entry.in { color: #10b981; border-color: #10b981; }
.log-entry.out { color: #3b82f6; border-color: #3b82f6; }

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulse 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const wsUrl = `ws://${window.location.hostname}:8085/ws/random`;
    const randomEl = document.getElementById('random-value');
    const timeEl = document.getElementById('last-updated');
    const statusEl = document.getElementById('connection-status');
    const logEl = document.getElementById('event-log');
    const pingEl = document.getElementById('ping-stats');
    
    let socket = null;
    let pingInterval = null;

    function addLog(msg, type = 'system') {
        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;
        entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
        logEl.prepend(entry);
        if (logEl.children.length > 50) logEl.lastChild.remove();
    }

    function connect() {
        addLog(`Connecting to ${wsUrl}...`);
        socket = new WebSocket(wsUrl);

        socket.onopen = () => {
            statusEl.textContent = 'Online';
            statusEl.className = 'stat-value status-online';
            addLog('WebSocket Connection Established', 'system');
            
            // Start heartbeat
            pingInterval = setInterval(() => {
                const start = Date.now();
                socket.send(JSON.stringify({ event: 'ping', startTime: start }));
            }, 5000);
        };

        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            
            if (data.event === 'random.number') {
                randomEl.textContent = data.data.value;
                timeEl.textContent = data.data.timestamp;
                
                // Add pulse effect
                randomEl.classList.remove('pulse');
                void randomEl.offsetWidth; // trigger reflow
                randomEl.classList.add('pulse');
                
                addLog(`Received: ${data.data.value}`, 'in');
            } else if (data.event === 'pong') {
                const latency = Date.now() - data.data.time * 1000; // Simplified latency check
                pingEl.textContent = `Heartbeat OK`;
            } else {
                addLog(`Info: ${data.message || 'Updated'}`, 'system');
            }
        };

        socket.onclose = () => {
            statusEl.textContent = 'Offline';
            statusEl.className = 'stat-value status-offline';
            addLog('Connection Lost. Retrying in 5s...', 'system');
            clearInterval(pingInterval);
            setTimeout(connect, 5000);
        };

        socket.onerror = (err) => {
            addLog('WebSocket Error observed', 'system');
            console.error(err);
        };
    }

    document.getElementById('clear-log').onclick = () => {
        logEl.innerHTML = '';
    };

    connect();
});
</script>
@endsection
