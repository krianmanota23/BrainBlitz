@extends('layouts.app')

@section('title', 'Arena Battle History')

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col p-6 md:p-10 space-y-12 pb-24 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <span class="text-xs font-black uppercase text-purple-400 tracking-[0.3em] italic">HISTORICAL BATTLE ARCHIVE</span>
            <h1 class="text-5xl font-black italic tracking-tighter uppercase leading-tight grow mt-1"><span class="text-gradient">ARENA BATTLE</span> HISTORY</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-2 text-xs">Review past quiz bowl results, squad leaderboards, champions & lowest scorers.</p>
        </div>
        <div class="flex items-center gap-4">
             <a href="{{ route('admin.quizzes.index') }}" class="btn-primary px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl flex items-center space-x-2">
                 <span>⚡ Launch New Arena</span>
             </a>
        </div>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="card p-8 rounded-3xl group hover:border-purple-500/50 transition-all">
             <div class="flex items-center justify-between mb-4">
                <span class="text-4xl">🏆</span>
                <span class="text-[9px] font-black uppercase text-purple-400 bg-purple-500/10 px-3 py-1 rounded-full border border-purple-500/20">TOTAL BATTLES</span>
             </div>
             <p class="text-5xl font-black italic tracking-tighter text-white tabular-nums">{{ $totalGames }}</p>
             <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mt-2">Completed Quiz Bowls</p>
        </div>

        <div class="card p-8 rounded-3xl group hover:border-pink-500/50 transition-all">
             <div class="flex items-center justify-between mb-4">
                <span class="text-4xl">👥</span>
                <span class="text-[9px] font-black uppercase text-pink-400 bg-pink-500/10 px-3 py-1 rounded-full border border-pink-500/20">SQUAD MEMBERS</span>
             </div>
             <p class="text-5xl font-black italic tracking-tighter text-white tabular-nums">{{ $totalPlayersJoined }}</p>
             <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mt-2">Total Joined Participants</p>
        </div>

        <div class="card p-8 rounded-3xl group hover:border-yellow-500/50 transition-all">
             <div class="flex items-center justify-between mb-4">
                <span class="text-4xl">👑</span>
                <span class="text-[9px] font-black uppercase text-yellow-400 bg-yellow-500/10 px-3 py-1 rounded-full border border-yellow-500/20">RECORD SCORE</span>
             </div>
             <p class="text-5xl font-black italic tracking-tighter text-yellow-400 tabular-nums">{{ number_format($allTimeTopScore) }}</p>
             <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mt-2">All-Time High Score</p>
        </div>
    </div>

    <!-- Battle Records Feed -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-gray-400 italic">ALL QUIZ BOWL SESSIONS</h2>
            <span class="text-xs font-black uppercase tracking-widest text-gray-500">{{ $historyData->count() }} ARENAS RECORDED</span>
        </div>

        <div class="space-y-6">
            @forelse($historyData as $item)
                @php 
                    $room = $item['room']; 
                    $winner = $item['winner'];
                    $second = $item['second'];
                    $third = $item['third'];
                    $last = $item['last'];
                @endphp
                
                <div class="card p-8 rounded-[2.5rem] border-white/10 space-y-6 group hover:border-purple-500/40 transition-all shadow-xl relative overflow-hidden">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
                        <div class="flex items-center space-x-6">
                            <div class="px-5 py-3 rounded-2xl bg-white/5 border border-white/10 text-center">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest block">CODE</span>
                                <span class="text-2xl font-black font-mono tracking-widest text-purple-400">{{ $room->room_code }}</span>
                            </div>
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-2xl font-black uppercase italic tracking-tighter text-white group-hover:text-purple-300 transition-colors">{{ $room->quiz->title }}</h3>
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                          @if($room->status == 'finished') bg-green-500/10 text-green-400 border-green-500/30 @elseif($room->status == 'ongoing') bg-yellow-500/10 text-yellow-400 border-yellow-500/30 @else bg-gray-500/10 text-gray-400 border-gray-500/30 @endif">
                                        {{ $room->status }}
                                    </span>
                                </div>
                                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">
                                    Hosted on {{ $room->created_at->format('M d, Y @ h:i A') }} • <span class="text-white">{{ $item['participant_count'] }} Players Joined</span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('admin.rooms.results', $room->id) }}" 
                               class="px-8 py-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-black uppercase tracking-widest text-xs shadow-lg transition-all flex items-center space-x-2">
                                <span>🏆 View Full Results</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Rankings Summary Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                        <!-- 1st Winner -->
                        <div class="p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/30 flex items-center space-x-4">
                            <span class="text-3xl">🥇</span>
                            <div class="overflow-hidden">
                                <span class="text-[8px] font-black uppercase tracking-widest text-yellow-400 block">CHAMPION</span>
                                <span class="text-base font-black uppercase italic text-white truncate block">
                                    {{ $winner ? $winner->user->nickname : 'No Player' }}
                                </span>
                                <span class="text-xs font-black italic text-yellow-300">
                                    {{ $winner ? number_format($winner->total_score) . ' PTS' : '0 PTS' }}
                                </span>
                            </div>
                        </div>

                        <!-- 2nd Runner Up -->
                        <div class="p-4 rounded-2xl bg-gray-500/10 border border-gray-500/30 flex items-center space-x-4">
                            <span class="text-3xl">🥈</span>
                            <div class="overflow-hidden">
                                <span class="text-[8px] font-black uppercase tracking-widest text-gray-400 block">2ND PLACE</span>
                                <span class="text-base font-black uppercase italic text-white truncate block">
                                    {{ $second ? $second->user->nickname : 'N/A' }}
                                </span>
                                <span class="text-xs font-black italic text-gray-300">
                                    {{ $second ? number_format($second->total_score) . ' PTS' : '0 PTS' }}
                                </span>
                            </div>
                        </div>

                        <!-- 3rd Place -->
                        <div class="p-4 rounded-2xl bg-amber-900/10 border border-amber-600/30 flex items-center space-x-4">
                            <span class="text-3xl">🥉</span>
                            <div class="overflow-hidden">
                                <span class="text-[8px] font-black uppercase tracking-widest text-amber-500 block">3RD PLACE</span>
                                <span class="text-base font-black uppercase italic text-white truncate block">
                                    {{ $third ? $third->user->nickname : 'N/A' }}
                                </span>
                                <span class="text-xs font-black italic text-amber-400">
                                    {{ $third ? number_format($third->total_score) . ' PTS' : '0 PTS' }}
                                </span>
                            </div>
                        </div>

                        <!-- Lowest Scorer ("Scored Last") -->
                        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center space-x-4">
                            <span class="text-3xl">🔻</span>
                            <div class="overflow-hidden">
                                <span class="text-[8px] font-black uppercase tracking-widest text-red-400 block">SCORED LAST</span>
                                <span class="text-base font-black uppercase italic text-white truncate block">
                                    {{ $last ? $last->user->nickname : 'N/A' }}
                                </span>
                                <span class="text-xs font-black italic text-red-400">
                                    {{ $last ? number_format($last->total_score) . ' PTS' : '0 PTS' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-24 card rounded-[3rem] border-white/10 space-y-4">
                    <span class="text-6xl block">📜</span>
                    <h3 class="text-2xl font-black uppercase italic tracking-tighter text-white">No Arena Battles Recorded Yet</h3>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">Launch a quiz bowl arena and host a game to view battle history here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
