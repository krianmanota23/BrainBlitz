@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col p-8 space-y-12 pb-24 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-tight grow"><span class="text-gradient inline-block pr-3 py-1">BATTLE</span><br> COMMAND</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4">Welcome back, <span class="text-white">Master Host</span>.</p>
        </div>
        <div class="flex items-center gap-4">
             <a href="{{ route('admin.quizzes.create') }}" class="btn-primary px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-2xl">Construct New Quiz</a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statIcons = [
                ['label' => 'Quizzes', 'val' => $stats['quizzes'], 'icon' => '📚', 'color' => 'purple'],
                ['label' => 'Topics', 'val' => $stats['topics'], 'icon' => '🏷️', 'color' => 'pink'],
                ['label' => 'Finished', 'val' => $stats['games'], 'icon' => '🏆', 'color' => 'yellow'],
                ['label' => 'Students', 'val' => $stats['students'], 'icon' => '👥', 'color' => 'blue'],
            ];
        @endphp
        @foreach($statIcons as $s)
        <div class="card p-8 rounded-3xl group hover:border-{{ $s['color'] }}-500/50 transition-all duration-300">
             <div class="flex items-center justify-between mb-4">
                <span class="text-4xl group-hover:scale-125 transition-transform duration-500">{{ $s['icon'] }}</span>
                <div class="h-8 w-px bg-white/5"></div>
             </div>
             <p class="text-4xl font-black italic tracking-tighter text-white tabular-nums">{{ $s['val'] }}</p>
             <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    @if($activeRoom)
    <!-- Ongoing Game Alert -->
    <div class="card p-8 rounded-3xl border-green-500/20 bg-green-500/5 flex flex-col md:flex-row items-center justify-between gap-8 animate-pulse shadow-[0_0_50px_rgba(34,197,94,0.1)]">
        <div class="flex items-center space-x-6">
            <div class="w-16 h-16 rounded-2xl bg-green-500/20 flex items-center justify-center text-3xl">🎮</div>
            <div>
                <h3 class="text-green-500 font-black uppercase tracking-widest text-xs mb-1">ARENA ACTIVE: {{ $activeRoom->room_code }}</h3>
                <p class="text-2xl font-black uppercase italic tracking-tighter text-white">{{ $activeRoom->quiz->title }}</p>
                <p class="text-[10px] font-bold text-gray-400 mt-1">{{ $activeRoom->participants->count() }} COMBATANTS LOCKED IN</p>
            </div>
        </div>
        <a href="{{ $activeRoom->status == 'waiting' ? route('admin.rooms.lobby', $activeRoom->id) : route('admin.rooms.game', $activeRoom->id) }}" 
           class="px-10 py-4 bg-green-600 hover:bg-green-500 rounded-xl font-black uppercase tracking-widest text-[10px] text-white transition-all">Rejoin Battle Command &rarr;</a>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Recent Quizzes -->
        <div class="lg:col-span-2 space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-gray-500 font-black uppercase tracking-[0.3em] text-xs">Recently Crafted Arenas</h3>
                <a href="{{ route('admin.quizzes.index') }}" class="text-[10px] font-black uppercase tracking-widest text-purple-400 hover:text-pink-400 transition-colors">View All Quizzes &rarr;</a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentQuizzes as $quiz)
                <div class="card p-6 rounded-2xl flex items-center justify-between group hover:border-white/20 transition-all">
                    <div class="flex items-center space-x-6">
                        <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-[8px] font-black uppercase tracking-widest {{ ($quiz->status == 'draft') ? 'text-gray-500' : 'text-purple-400' }}">{{ $quiz->status }}</span>
                        <div>
                            <p class="text-lg font-black uppercase italic tracking-tighter text-white group-hover:text-purple-400 transition-colors">{{ $quiz->title }}</p>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">{{ $quiz->questions_count }} CHALLENGES</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.quizzes.show', $quiz->id) }}" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors text-xs">&rarr;</a>
                </div>
                @empty
                <div class="text-center py-20 card rounded-3xl">
                    <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">No arenas found yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-8">
            <h3 class="text-gray-500 font-black uppercase tracking-[0.3em] text-xs">Battle Command Quick Actions</h3>
            <div class="grid grid-cols-1 gap-4">
                <a href="{{ route('admin.quizzes.create') }}" class="card p-6 rounded-2xl flex items-center space-x-6 hover:bg-white/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">➕</div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-white">Create New Arena</p>
                        <p class="text-[9px] font-bold text-gray-600 uppercase tracking-widest mt-1 italic">Draft a new battlefield</p>
                    </div>
                </a>
                <a href="{{ route('admin.topics.index') }}" class="card p-6 rounded-2xl flex items-center space-x-6 hover:bg-white/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🏷️</div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-white">Manage Topics</p>
                        <p class="text-[9px] font-bold text-gray-600 uppercase tracking-widest mt-1 italic">Organize challenge categories</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
