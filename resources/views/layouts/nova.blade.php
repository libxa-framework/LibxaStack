<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} | LibxaNova</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.05); }
        .sidebar-link.active { background: #3b82f6; color: white; }
    </style>
</head>
<body class="h-full text-slate-200">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="w-64 glass hidden md:flex flex-col border-r border-slate-800">
            <div class="p-6">
                <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                    LibxaNova
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                <a href="/admin" class="flex items-center px-4 py-3 rounded-xl sidebar-link {{ request()->is('admin') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <div class="mt-8 mb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Resources</div>
                
                @php $resources = app('nova')->all(); @endphp
                @foreach($resources as $key => $resource)
                    <a href="/admin/resources/{{ $key }}" class="flex items-center px-4 py-3 rounded-xl sidebar-link {{ request()->is("admin/resources/{$key}*") ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        {{ $resource::label() }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center p-2 rounded-lg bg-slate-800/50">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <div class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-950">
            <!-- Header -->
            <header class="h-16 glass flex items-center justify-between px-8 z-10">
                <h1 class="text-xl font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                <div class="flex items-center space-y-4">
                    <form action="/logout" method="POST">
                        <button class="text-sm text-slate-400 hover:text-white transition">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
