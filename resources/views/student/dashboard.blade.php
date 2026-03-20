@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
@include('student.partials.navbar')

<div class="flex-1 flex flex-col p-8 space-y-12 pb-24 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-none grow"><span class="text-gradient">STUDENT</span><br> COMMAND</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4">Welcome back, <span class="text-white">{{ Auth::user()->nickname }}</span>!</p>
        </div>
        <div class="flex items-center gap-4">
             <a href="{{ route('student.join') }}" class="btn-primary px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-2xl pulse-glow">Enter Arena Room</a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statIcons = [
                ['label' => 'Games Played', 'val' => $stats['games_played'], 'icon' => '🎮', 'color' => 'purple'],
                ['label' => 'Best Rank', 'val' => '#'.$stats['best_rank'], 'icon' => '👑', 'color' => 'yellow'],
                ['label' => 'Total Score', 'val' => number_format($stats['total_score']), 'icon' => '⚡', 'color' => 'pink'],
                ['label' => 'Accuracy %', 'val' => $stats['accuracy'].'%', 'icon' => '🎯', 'color' => 'green'],
            ];
        @endphp
        @foreach($statIcons as $s)
        <div class="card p-8 rounded-3xl group hover:border-{{ $s['color'] }}-500/50 transition-all duration-300">
             <div class="flex items-center justify-between mb-4">
                <span class="text-4xl group-hover:scale-125 transition-transform duration-500">{{ $s['icon'] }}</span>
                <div class="h-8 w-px bg-white/5"></div>
             </div>
             <p class="text-3xl font-black italic tracking-tighter text-white tabular-nums">{{ $s['val'] }}</p>
             <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pt-8">
        <!-- Main Action & Profile -->
        <div class="space-y-8 h-full">
            <a href="{{ route('student.join') }}" 
               class="card p-12 rounded-[3.5rem] bg-gradient-to-br from-purple-600/10 to-pink-600/10 border-purple-500/20 shadow-[0_0_80px_rgba(168,85,247,0.1)] group hover:scale-[1.02] transition-all duration-500 relative overflow-hidden flex flex-col items-center justify-center text-center space-y-8 h-96">
                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="text-8xl group-hover:rotate-12 transition-transform duration-500">🎮</div>
                <div class="space-y-4 relative z-10">
                    <h2 class="text-5xl font-black uppercase text-gradient italic tracking-tighter">JOIN A GAME</h2>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs max-w-[200px] mx-auto italic">Enter your room code to join a battle!</p>
                </div>
            </a>

            <div class="card p-8 rounded-3xl border-white/5 group">
                 <h3 class="text-gray-500 font-black uppercase tracking-widest text-[10px] mb-6">Warrior Profile</h3>
                 <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-3xl font-black italic text-white drop-shadow-lg">
                        {{ substr(Auth::user()->nickname, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xl font-black uppercase text-white tracking-tighter">{{ Auth::user()->full_name }}</p>
                        <p class="text-xs font-bold text-gray-400">@ {{ Auth::user()->username }}</p>
                    </div>
                 </div>
                 <a href="{{ route('profile') }}" class="block mt-6 text-[10px] font-black uppercase tracking-widest text-[#94a3b8] hover:text-white transition-colors">Edit Sync Profiles &rarr;</a>
            </div>
        </div>

        <!-- Recent Games -->
        <div class="lg:col-span-2 space-y-8 h-full">
             <div class="flex items-center justify-between">
                <h3 class="text-gray-500 font-black uppercase tracking-[0.3em] text-xs">Recent Battle Performance</h3>
                <span class="text-[10px] font-black italic text-purple-400 uppercase tracking-widest">Post-Session Detailed Data</span>
             </div>
             
             <div class="space-y-4">
                @forelse($recentGames as $game)
                    @php $score = $game->room->scores->first(); @endphp
                    <div class="card p-6 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group hover:border-purple-500/30 transition-all">
                        <div class="flex items-center space-x-6">
                            <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">⚔️</div>
                            <div>
                                <p class="text-lg font-black uppercase italic tracking-tighter text-white group-hover:text-gradient">{{ $game->room->quiz->title }}</p>
                                <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest mt-1">{{ $game->joined_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-8 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="text-right">
                                <p class="text-xl font-black italic text-purple-400">#{{ $score->rank ?? 'N/A' }}</p>
                                <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest">{{ $score->total_score ?? 0 }} PTS</p>
                            </div>
                            <a href="{{ route('student.rooms.results', $game->room_id) }}" class="px-6 py-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 text-[9px] font-black uppercase tracking-widest text-white transition-all">Review</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-32 card rounded-3xl">
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">The arena call remains unanswered. Join a game to start your history.</p>
                    </div>
                @endforelse
             </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 20px rgba(168,85,247,0.2); } 50% { box-shadow: 0 0 40px rgba(168,85,247,0.5); } }
    .pulse-glow { animation: pulse-glow 3s infinite; }
</style>
@endsection
