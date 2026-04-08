@extends('layouts.nova')

@section('content')
<div class="mb-12">
    <h2 class="text-3xl font-bold text-white mb-2">Welcome back, {{ auth()->user()->name ?? 'Admin' }}!</h2>
    <p class="text-slate-400">Here's what's happening across your LibxaFrame application today.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="glass p-8 rounded-3xl shadow-xl border-l-4 border-blue-500">
        <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Total Resources</div>
        <div class="text-4xl font-black text-white">{{ count($resources) }}</div>
    </div>
    
    <div class="glass p-8 rounded-3xl shadow-xl border-l-4 border-indigo-500">
        <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Active Sessions</div>
        <div class="text-4xl font-black text-white">12</div>
    </div>

    <div class="glass p-8 rounded-3xl shadow-xl border-l-4 border-emerald-500">
        <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Framework Load</div>
        <div class="text-4xl font-black text-white">2.4ms</div>
    </div>
</div>

<h3 class="text-xl font-bold text-white mb-6">Quick Manage</h3>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    @foreach($resources as $key => $resource)
        <a href="/admin/resources/{{ $key }}" class="glass p-6 rounded-2xl group hover:scale-105 transition active:scale-95">
            <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-blue-600 transition">
                <svg class="w-6 h-6 text-blue-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div class="font-bold text-white mb-1">{{ $resource::label() }}</div>
            <div class="text-sm text-slate-500">Manage {{ strtolower($resource::label()) }} and data.</div>
        </a>
    @endforeach
</div>
@endsection
