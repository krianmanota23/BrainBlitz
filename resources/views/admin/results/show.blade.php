@extends('layouts.app')

@section('title', 'Final Ceremony: ' . $room->quiz->title)

@section('content')
<div class="flex-1 flex flex-col p-8 space-y-24 bg-[#0a0a14] min-h-screen overflow-x-hidden relative" x-data="{ tab: 'podium' }">
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none opacity-20">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-600 rounded-full blur-[150px] animate-pulse"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-pink-600 rounded-full blur-[150px] animate-pulse"></div>
    </div>

    <!-- Header -->
    <div class="text-center z-10 animate-in fade-in slide-in-from-top duration-1000">
        <h1 class="text-3xl font-black italic tracking-tighter uppercase"><span class="text-gradient">BRAIN</span>BLITZ RESULTS</h1>
        <h2 class="text-7xl font-black uppercase tracking-tighter text-white mt-4 italic">BATTLE <span class="text-gradient">COMPLETE</span></h2>
    </div>

    <!-- SECTION 1: WINNER PODIUM -->
    <div class="flex flex-col items-center justify-center space-y-12">
        <div class="flex items-end justify-center space-x-4 md:space-x-12 h-96">
            <!-- 2nd Place -->
            @if($scores->count() >= 2)
            <div class="flex flex-col items-center animate-in slide-in-from-bottom duration-700 delay-300">
                <div class="mb-6 text-center">
                    <span class="text-4xl">🥈</span>
                    <p class="text-xl font-black uppercase italic text-white mt-2">{{ $scores[1]->user->nickname }}</p>
                    <p class="text-3xl font-black italic text-gray-500">{{ $scores[1]->total_score }}</p>
                </div>
                <div class="w-32 md:w-48 bg-gradient-to-t from-gray-700 to-gray-500/30 rounded-t-3xl border-t-4 border-gray-400/50 shadow-2xl h-40"></div>
            </div>
            @endif

            <!-- 1st Place -->
            @if($scores->count() >= 1)
            <div class="flex flex-col items-center animate-in slide-in-from-bottom duration-1000 relative">
                <!-- Confetti Placeholder/Effect -->
                <div class="absolute -top-24 left-1/2 transform -translate-x-1/2 w-96 h-96 pointer-events-none z-20">
                     <div class="confetti"></div>
                </div>

                <div class="mb-8 text-center z-10">
                    <span class="text-7xl drop-shadow-[0_0_20px_rgba(255,215,0,0.5)]">👑</span>
                    <p class="text-4xl font-black uppercase italic text-white mt-4 tracking-tighter">{{ $scores[0]->user->nickname }}</p>
                    <p class="text-6xl font-black italic text-[#FFD700] drop-shadow-lg">{{ $scores[0]->total_score }}</p>
                </div>
                <div class="w-40 md:w-64 bg-gradient-to-t from-yellow-700 to-yellow-500/40 rounded-t-[3rem] border-t-4 border-[#FFD700] shadow-[0_0_50px_rgba(255,215,0,0.2)] h-64"></div>
            </div>
            @endif

            <!-- 3rd Place -->
            @if($scores->count() >= 3)
            <div class="flex flex-col items-center animate-in slide-in-from-bottom duration-700 delay-500">
                <div class="mb-4 text-center">
                    <span class="text-4xl">🥉</span>
                    <p class="text-lg font-black uppercase italic text-white mt-2">{{ $scores[2]->user->nickname }}</p>
                    <p class="text-2xl font-black italic text-amber-700">{{ $scores[2]->total_score }}</p>
                </div>
                <div class="w-28 md:w-40 bg-gradient-to-t from-amber-900 to-amber-700/30 rounded-t-2xl border-t-4 border-amber-800 shadow-2xl h-32"></div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats and Leaderboard Toggle -->
    <div class="max-w-7xl mx-auto w-full z-10">
        <div class="flex justify-center mb-12">
            <div class="bg-white/5 p-2 rounded-2xl border border-white/10 flex space-x-4">
                <button @click="tab = 'podium'" :class="tab == 'podium' ? 'bg-purple-600' : 'hover:bg-white/5'" class="px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs transition-all">Scoreboard</button>
                <button @click="tab = 'stats'" :class="tab == 'stats' ? 'bg-purple-600' : 'hover:bg-white/5'" class="px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs transition-all">Battle Stats</button>
            </div>
        </div>

        <!-- FULL LEADERBOARD -->
        <div x-show="tab == 'podium'" class="space-y-4 animate-in fade-in duration-500">
            <div class="grid grid-cols-6 px-10 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                <div class="col-span-1">Rank</div>
                <div class="col-span-2">Nickname</div>
                <div class="col-span-1 text-center">Correct</div>
                <div class="col-span-1 text-center">Points</div>
                <div class="col-span-1 text-right">Accuracy</div>
            </div>
            @foreach($scores as $s)
            <div class="card p-6 rounded-2xl border-white/10 grid grid-cols-6 items-center px-10 group hover:border-purple-500/30 transition-all
                        @if($loop->index == 0) bg-yellow-500/5 @elseif($loop->index == 1) bg-gray-500/5 @elseif($loop->index == 2) bg-amber-900/5 @endif">
                <div class="col-span-1">
                    @if($loop->index < 3)
                        <span class="text-2xl">@if($loop->index == 0) 🥇 @elseif($loop->index == 1) 🥈 @else 🥉 @endif</span>
                    @else
                        <span class="text-xl font-black text-gray-500">{{ $s->rank }}</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <p class="text-xl font-black uppercase italic text-white group-hover:text-purple-400 transition-colors">{{ $s->user->nickname }}</p>
                    <p class="text-[10px] text-gray-500 font-bold">{{ $s->user->full_name }}</p>
                </div>
                <div class="col-span-1 text-center font-black text-gray-400">
                    {{ $s->user->answers->where('room_id', $room->id)->where('is_correct', true)->count() }} / {{ $room->quiz->questions->count() }}
                </div>
                <div class="col-span-1 text-center text-2xl font-black italic text-white tracking-tighter">{{ $s->total_score }}</div>
                <div class="col-span-1 text-right">
                    @php
                        $correct = $s->user->answers->where('room_id', $room->id)->where('is_correct', true)->count();
                        $acc = $room->quiz->questions->count() > 0 ? round(($correct / $room->quiz->questions->count()) * 100) : 0;
                    @endphp
                    <span class="px-3 py-1 rounded-lg font-black text-[10px] @if($acc >= 70) bg-green-500/10 text-green-500 @elseif($acc >= 40) bg-yellow-500/10 text-yellow-500 @else bg-red-500/10 text-red-500 @endif">
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
                        <h4 class="text-xl font-black uppercase italic tracking-tighter text-white grow">{{ Str::limit($stat['question']->question_text, 60) }}</h4>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black italic tracking-tighter"
                              :class="{{ $stat['accuracy'] }} >= 70 ? 'text-green-500' : ({{ $stat['accuracy'] }} >= 40 ? 'text-yellow-500' : 'text-red-500')">
                              {{ $stat['accuracy'] }}%
                        </span>
                        <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest">Accuracy</p>
                    </div>
                </div>

                <div class="w-full bg-white/5 h-3 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full transition-all duration-1000"
                         style="width: {{ $stat['accuracy'] }}%"
                         :class="{{ $stat['accuracy'] }} >= 70 ? 'bg-green-500' : ({{ $stat['accuracy'] }} >= 40 ? 'bg-yellow-500' : 'bg-red-500')"></div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-white/5">
                     <div class="flex flex-col">
                        <span class="text-[8px] font-black text-gray-600 uppercase tracking-widest">Best Performer</span>
                        <span class="text-xs font-black text-purple-400">@if($stat['total'] > 0) Fastest Blitz @else No Responses @endif</span>
                     </div>
                     <div class="text-right">
                        <span class="text-[8px] font-black text-gray-600 uppercase tracking-widest">Correct Option</span>
                        <span class="text-xs font-black text-green-500 uppercase">{{ $stat['question']->options->where('is_correct', true)->first()->option_text }}</span>
                     </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="max-w-7xl mx-auto w-full pb-24 z-10">
        <div class="card p-8 rounded-3xl border-white/10 flex flex-wrap items-center justify-center gap-6">
            <a href="{{ route('admin.rooms.results.export', $room->id) }}" class="btn-primary px-10 py-4 rounded-xl font-black uppercase tracking-widest text-xs shadow-xl">Export CSV Data</a>
            <form action="{{ route('admin.quizzes.launch', $room->quiz_id) }}" method="POST">
                @csrf
                <button type="submit" class="px-10 py-4 rounded-xl bg-green-600 hover:bg-green-500 font-black uppercase tracking-widest text-xs shadow-xl transition-all">Launch New Room</button>
            </form>
            <a href="{{ route('admin.quizzes.index') }}" class="px-10 py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-xs transition-all">Back to Home</a>
        </div>
    </div>
</div>

<style>
.confetti {
  width: 10px;
  height: 10px;
  background-color: #ffd700;
  position: absolute;
  top: -10px;
  animation: fall 3s infinite linear;
}
@keyframes fall {
  to { transform: translateY(500px) rotate(360deg); opacity: 0; }
}
/* More dynamic confetti can be added here */
</style>
@endsection
