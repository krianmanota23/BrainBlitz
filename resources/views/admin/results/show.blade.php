@extends('layouts.app')

@section('title', 'Final Ceremony: ' . $room->quiz->title)

@section('content')
<div class="flex-1 flex flex-col p-6 md:p-10 space-y-12 bg-[#0a0a14] min-h-screen relative overflow-x-hidden" x-data="{ tab: 'podium' }">

    <!-- Persistent Top Admin Navigation Bar -->
    <nav class="w-full bg-white/5 border border-white/10 backdrop-blur-2xl rounded-3xl p-4 px-8 flex items-center justify-between z-50 shadow-2xl">
        <div class="flex items-center space-x-4">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black italic text-lg shadow-[0_0_20px_rgba(168,85,247,0.5)] text-white">⚡</div>
            <div>
                <span class="text-xl font-black italic tracking-tighter uppercase text-white"><span class="text-gradient">BRAIN</span>BLITZ ADMIN</span>
                <span class="block text-[9px] font-black uppercase text-gray-500 tracking-widest">Arena Results Center</span>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-white/10 hover:bg-white/10 text-white font-black text-xs uppercase tracking-widest transition-all flex items-center space-x-2">
                <span>🏠 Dashboard</span>
            </a>
            <a href="{{ route('admin.quizzes.index') }}" class="px-5 py-2.5 rounded-xl border border-white/10 hover:bg-white/10 text-white font-black text-xs uppercase tracking-widest transition-all flex items-center space-x-2">
                <span>🎮 Quizzes</span>
            </a>
            <a href="{{ route('admin.rooms.results.export', $room->id) }}" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-black text-xs uppercase tracking-widest shadow-lg transition-all flex items-center space-x-2">
                <span>📥 Export CSV</span>
            </a>
        </div>
    </nav>

    <!-- Background Glow -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none opacity-20">
        <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-purple-600 rounded-full blur-[180px] animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-pink-600 rounded-full blur-[180px] animate-pulse"></div>
    </div>

    <!-- GRAND CHAMPION ANNOUNCEMENT BANNER -->
    @if($scores->count() > 0)
        @php $winner = $scores->first(); @endphp
        <div class="relative w-full max-w-5xl mx-auto rounded-[3.5rem] bg-gradient-to-r from-yellow-500/20 via-purple-600/30 to-yellow-500/20 border-2 border-yellow-400/50 p-10 md:p-14 text-center shadow-[0_0_100px_rgba(255,215,0,0.2)] animate-in fade-in zoom-in duration-1000 overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 px-8 py-2 bg-yellow-400 text-black font-black uppercase text-xs tracking-[0.3em] rounded-b-2xl shadow-lg italic">
                👑 VICTORY ANNOUNCEMENT
            </div>

            <div class="space-y-6 pt-4 relative z-10">
                <span class="text-7xl md:text-8xl block animate-bounce drop-shadow-[0_0_30px_rgba(255,215,0,0.8)]">🏆</span>
                
                <div class="space-y-2">
                    <p class="text-gray-300 font-black uppercase tracking-[0.4em] text-xs italic">CROWNING THE CHAMPION</p>
                    <h1 class="text-5xl md:text-7xl font-black uppercase italic tracking-tighter text-yellow-300 drop-shadow-[0_0_30px_rgba(255,215,0,0.6)]">
                        {{ $winner->user->nickname }}
                    </h1>
                </div>

                <div class="inline-flex items-center space-x-6 bg-black/40 px-8 py-4 rounded-2xl border border-yellow-400/30 backdrop-blur-xl">
                    <div>
                        <span class="text-[9px] font-black uppercase text-gray-400 tracking-widest block">Total Score</span>
                        <span class="text-3xl font-black italic text-yellow-400">{{ number_format($winner->total_score) }} PTS</span>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                    <div>
                        <span class="text-[9px] font-black uppercase text-gray-400 tracking-widest block">Arena Title</span>
                        <span class="text-xl font-black italic text-white uppercase">{{ $room->quiz->title }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- WINNER PODIUM DISPLAY -->
    <div class="max-w-6xl mx-auto w-full pt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
            <!-- 2nd Place -->
            @if($scores->count() >= 2)
            @php $second = $scores[1]; @endphp
            <div class="card p-8 rounded-[3rem] border-2 border-gray-400/40 bg-gradient-to-b from-gray-500/10 to-transparent text-center space-y-4 shadow-2xl relative order-2 md:order-1 transform hover:scale-105 transition-all">
                <span class="text-5xl block">🥈</span>
                <span class="px-4 py-1 bg-gray-400/20 text-gray-300 border border-gray-400/40 rounded-full text-[10px] font-black uppercase tracking-widest italic">2ND PLACE</span>
                <h3 class="text-3xl font-black uppercase italic tracking-tighter text-white">{{ $second->user->nickname }}</h3>
                <p class="text-4xl font-black italic text-gray-400">{{ number_format($second->total_score) }} PTS</p>
            </div>
            @endif

            <!-- 1st Place -->
            @if($scores->count() >= 1)
            @php $first = $scores[0]; @endphp
            <div class="card p-10 rounded-[3.5rem] border-4 border-yellow-400 bg-gradient-to-b from-yellow-500/20 to-purple-900/20 text-center space-y-6 shadow-[0_0_80px_rgba(255,215,0,0.3)] relative order-1 md:order-2 transform hover:scale-105 transition-all z-20">
                <span class="text-6xl block animate-pulse">👑</span>
                <span class="px-6 py-2 bg-yellow-400 text-black rounded-full text-xs font-black uppercase tracking-widest italic shadow-lg">1ST PLACE CHAMPION</span>
                <h3 class="text-4xl font-black uppercase italic tracking-tighter text-yellow-300">{{ $first->user->nickname }}</h3>
                <p class="text-5xl font-black italic text-yellow-400 drop-shadow-lg">{{ number_format($first->total_score) }} PTS</p>
            </div>
            @endif

            <!-- 3rd Place -->
            @if($scores->count() >= 3)
            @php $third = $scores[2]; @endphp
            <div class="card p-8 rounded-[3rem] border-2 border-amber-600/40 bg-gradient-to-b from-amber-700/10 to-transparent text-center space-y-4 shadow-2xl relative order-3 transform hover:scale-105 transition-all">
                <span class="text-5xl block">🥉</span>
                <span class="px-4 py-1 bg-amber-600/20 text-amber-400 border border-amber-600/40 rounded-full text-[10px] font-black uppercase tracking-widest italic">3RD PLACE</span>
                <h3 class="text-3xl font-black uppercase italic tracking-tighter text-white">{{ $third->user->nickname }}</h3>
                <p class="text-4xl font-black italic text-amber-500">{{ number_format($third->total_score) }} PTS</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Pumping Scroll Down Indicator -->
    <div class="flex flex-col items-center justify-center py-6 animate-bounce select-none z-10">
        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-purple-400 mb-2 italic">Scroll Down For Battle Breakdown & Leaderboard</span>
        <div class="w-8 h-12 rounded-full border-2 border-purple-500/50 flex items-start justify-center p-2 shadow-[0_0_20px_rgba(168,85,247,0.4)]">
            <div class="w-2 h-3 bg-gradient-to-b from-purple-400 to-pink-500 rounded-full animate-pulse"></div>
        </div>
        <svg class="w-5 h-5 text-purple-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
    </div>

    <!-- Tab Selector -->
    <div class="max-w-6xl mx-auto w-full z-10">
        <div class="flex justify-center mb-8">
            <div class="bg-white/5 p-2 rounded-2xl border border-white/10 flex space-x-4">
                <button @click="tab = 'podium'" :class="tab == 'podium' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:bg-white/5'" class="px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs transition-all select-none">
                    🏆 Squad Scoreboard
                </button>
                <button @click="tab = 'stats'" :class="tab == 'stats' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:bg-white/5'" class="px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs transition-all select-none">
                    📊 Battle Breakdown
                </button>
            </div>
        </div>

        <!-- FULL LEADERBOARD -->
        <div x-show="tab == 'podium'" class="space-y-4 animate-in fade-in duration-500">
            <div class="grid grid-cols-6 px-10 py-4 text-xs font-black text-purple-300 uppercase tracking-widest bg-white/5 border border-white/10 rounded-2xl shadow-md mb-2">
                <div class="col-span-1 flex items-center space-x-2">
                    <span>🏆 RANK</span>
                </div>
                <div class="col-span-2 flex items-center space-x-2">
                    <span>👤 COMBATANT</span>
                </div>
                <div class="col-span-1 text-center flex items-center justify-center space-x-1">
                    <span>🎯 CORRECT</span>
                </div>
                <div class="col-span-1 text-center flex items-center justify-center space-x-1">
                    <span>⚡ TOTAL POINTS</span>
                </div>
                <div class="col-span-1 text-right flex items-center justify-end space-x-1">
                    <span>📈 ACCURACY</span>
                </div>
            </div>
            @foreach($scores as $s)
            @php
                $userAnswers = $s->user?->answers ? $s->user->answers->where('room_id', $room->id) : collect([]);
                $correct = $userAnswers->where('is_correct', true)->count();
                $acc = $room->quiz->questions->count() > 0 ? round(($correct / $room->quiz->questions->count()) * 100) : 0;
            @endphp
            <div class="card p-6 rounded-2xl border-white/10 grid grid-cols-6 items-center px-10 group hover:border-purple-500/30 transition-all
                        @if($loop->index == 0) bg-yellow-500/10 border-yellow-500/30 @elseif($loop->index == 1) bg-gray-500/10 border-gray-500/30 @elseif($loop->index == 2) bg-amber-900/10 border-amber-600/30 @endif">
                <div class="col-span-1">
                    @if($loop->index < 3)
                        <span class="text-3xl">@if($loop->index == 0) 🥇 @elseif($loop->index == 1) 🥈 @else 🥉 @endif</span>
                    @else
                        <span class="text-2xl font-black text-gray-400">#{{ $s->rank }}</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <p class="text-2xl font-black uppercase italic text-white group-hover:text-purple-400 transition-colors">{{ $s->user->nickname ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500 font-bold">{{ $s->user->full_name ?? '' }}</p>
                </div>
                <div class="col-span-1 text-center font-black text-gray-300 text-lg">
                    {{ $correct }} / {{ $room->quiz->questions->count() }}
                </div>
                <div class="col-span-1 text-center text-3xl font-black italic text-white tracking-tighter">{{ number_format($s->total_score) }}</div>
                <div class="col-span-1 text-right">
                    <span class="px-4 py-2 rounded-xl font-black text-xs @if($acc >= 70) bg-green-500/10 text-green-400 border border-green-500/30 @elseif($acc >= 40) bg-yellow-500/10 text-yellow-400 border border-yellow-500/30 @else bg-red-500/10 text-red-400 border border-red-500/30 @endif">
                        {{ $acc }}%
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- QUESTION STATS -->
        <div x-show="tab == 'stats'" class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-in fade-in duration-500">
            @foreach($questionStats as $stat)
            <div class="card p-8 rounded-[2.5rem] border-white/10 space-y-6 group hover:border-purple-500/30 transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block mb-1">Question {{ $stat['question']->order_number }}</span>
                        <h4 class="text-2xl font-black uppercase italic tracking-tighter text-white grow">{{ Str::limit($stat['question']->question_text, 60) }}</h4>
                    </div>
                    <div class="text-right">
                        <span class="text-4xl font-black italic tracking-tighter text-purple-400">
                            {{ $stat['accuracy'] }}%
                        </span>
                        <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Accuracy</p>
                    </div>
                </div>

                <div class="w-full bg-white/5 h-3 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 transition-all duration-1000"
                         style="width: {{ $stat['accuracy'] }}%"></div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-white/5">
                     <div class="flex flex-col">
                        <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Total Responses</span>
                        <span class="text-sm font-black text-white">{{ $stat['total'] }} Answers</span>
                     </div>
                     <div class="text-right">
                        <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Correct Answer</span>
                        <span class="text-sm font-black text-green-400 uppercase">{{ $stat['question']->options->where('is_correct', true)->first()?->option_text ?? 'N/A' }}</span>
                     </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Footer Actions Bar -->
    <div class="max-w-6xl mx-auto w-full pb-20 z-10">
        <div class="card p-8 rounded-3xl border-white/10 flex flex-wrap items-center justify-between gap-6">
            <a href="{{ route('admin.rooms.results.export', $room->id) }}" class="btn-primary px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl flex items-center space-x-2">
                <span>📥 Export CSV Report</span>
            </a>
            
            <div class="flex items-center space-x-4">
                <form action="{{ route('admin.quizzes.launch', $room->quiz_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-10 py-4 rounded-2xl bg-green-600 hover:bg-green-500 text-white font-black uppercase tracking-widest text-xs shadow-xl transition-all">
                        ⚡ Launch New Arena
                    </button>
                </form>
                <a href="{{ route('admin.dashboard') }}" class="px-10 py-4 rounded-2xl border border-white/10 hover:bg-white/5 text-white font-black uppercase tracking-widest text-xs transition-all">
                    🏠 Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
