@extends('layouts.app')

@section('title', 'Your Results: ' . $room->quiz->title)

@section('content')
<div class="flex-1 flex flex-col p-8 space-y-12 bg-[#0a0a14] min-h-screen relative overflow-x-hidden" 
     x-data="{ count: 0, final: {{ $myScore->total_score ?? 0 }}, acc: {{ $accuracy }} } "
     x-init="let timer = setInterval(() => { if(count < final) count += Math.ceil(final / 50); else { count = final; clearInterval(timer); } }, 20)">
    
    <!-- Header Summary -->
    <div class="text-center space-y-4 animate-in fade-in duration-700">
        <div class="flex flex-col items-center">
             @if(($myScore->rank ?? 0) == 1)
                <span class="text-8xl mb-6 drop-shadow-[0_0_20px_rgba(255,215,0,0.5)]">👑</span>
                <h1 class="text-6xl font-black italic tracking-tighter uppercase text-[#FFD700]">BATTLE WINNER</h1>
             @elseif(($myScore->rank ?? 0) == 2)
                <span class="text-7xl mb-6 drop-shadow-[0_0_20px_rgba(192,192,192,0.5)]">🥈</span>
                <h1 class="text-5xl font-black italic tracking-tighter uppercase text-[#C0C0C0]">2ND PLACE</h1>
             @elseif(($myScore->rank ?? 0) == 3)
                <span class="text-6xl mb-6 drop-shadow-[0_0_20px_rgba(205,127,50,0.5)]">🥉</span>
                <h1 class="text-4xl font-black italic tracking-tighter uppercase text-[#CD7F32]">3RD PLACE</h1>
             @else
                <h1 class="text-4xl font-black italic tracking-tighter uppercase text-gray-500">GOOD BATTLE!</h1>
             @endif
        </div>
        <p class="text-gray-500 font-bold uppercase tracking-[0.3em] text-[10px] mt-4">{{ $room->quiz->title }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto w-full">
        <!-- Score Card -->
        <div class="card p-10 rounded-[3rem] border-white/10 flex flex-col items-center justify-center text-center space-y-2 group hover:border-purple-500/30 transition-all shadow-2xl">
             <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Total Performance Score</span>
             <h2 class="text-8xl font-black italic tracking-tighter text-gradient tabular-nums" x-text="count"></h2>
             <span class="text-sm font-black text-purple-400 uppercase tracking-tighter">Your Final Rank: #{{ $myScore->rank ?? 'N/A' }}</span>
        </div>

        <!-- Accuracy Circle -->
        <div class="card p-10 rounded-[3rem] border-white/10 flex flex-col items-center justify-center text-center space-y-6 group hover:border-purple-500/30 transition-all shadow-2xl">
             <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Blitz Accuracy</span>
             <div class="relative w-40 h-40">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="80" cy="80" r="70" class="stroke-current text-white/5 fill-none" stroke-width="12"></circle>
                    <circle cx="80" cy="80" r="70" class="stroke-current" 
                            :class="acc >= 70 ? 'text-green-500' : (acc >= 40 ? 'text-yellow-500' : 'text-red-500')" 
                            stroke-width="12" stroke-linecap="round" fill="none"
                            stroke-dasharray="439.8"
                            :stroke-dashoffset="439.8 - (439.8 * acc / 100)"
                            style="transition: stroke-dashoffset 2s ease-out"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-black italic tracking-tighter" x-text="acc + '%'"></span>
                </div>
             </div>
             <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Correct Answers: <span class="text-white">{{ $myAnswers->where('is_correct', true)->count() }}</span></p>
        </div>
    </div>

    <!-- Question Breakdown List -->
    <div class="max-w-4xl mx-auto w-full space-y-8 pt-12">
        <h3 class="text-3xl font-black italic tracking-tighter uppercase text-white grow border-l-4 border-purple-500 pl-6">CHALLENGE <span class="text-gradient">RECAP</span></h3>
        <div class="grid grid-cols-1 gap-4">
            @foreach($room->quiz->questions->sortBy('order_number') as $q)
                @php
                    $ans = $myAnswers->where('question_id', $q->id)->first();
                    $isCorrect = $ans?->is_correct;
                    $points = $ans?->points_earned ?? 0;
                @endphp
                <div class="card p-6 rounded-2xl border-white/10 flex items-center justify-between group">
                    <div class="flex items-center space-x-6">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl italic transition-all
                                   @if($isCorrect) bg-green-500/20 text-green-500 border border-green-500/30 @else bg-red-500/20 text-red-500 border border-red-500/30 @endif">
                             Q{{ $q->order_number }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-white uppercase tracking-tighter mb-1">{{ Str::limit($q->question_text, 40) }}</p>
                            @if($ans)
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Selected: <span class="text-white">{{ $ans->option->option_text }}</span></p>
                            @else
                                <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest">No Response</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                         <span class="text-xl font-black italic tracking-tighter @if($points > 0) text-gradient @else text-gray-700 @endif">{{ $points > 0 ? '+'.$points : '+0' }}</span>
                         <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest">Points Received</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- FINAL FULL LEADERBOARD -->
    <div class="max-w-4xl mx-auto w-full space-y-8 pt-12 pb-24">
        <h3 class="text-3xl font-black italic tracking-tighter uppercase text-white grow border-l-4 border-pink-500 pl-6">SQUAD <span class="text-gradient">RANKINGS</span></h3>
        <div class="space-y-3">
            @foreach($scoreboard as $s)
                <div class="card p-4 rounded-xl border-white/5 flex items-center justify-between px-8 @if($s->user_id == Auth::id()) border-purple-500/50 bg-purple-500/10 @endif">
                     <div class="flex items-center space-x-6">
                        <span class="text-lg font-black italic text-gray-500 w-6">#{{ $s->rank }}</span>
                        <span class="text-xl font-black uppercase italic tracking-tighter text-white">{{ $s->user->nickname }} @if($s->user_id == Auth::id()) <span class="text-[8px] text-purple-400 bg-purple-400/10 px-2 py-0.5 rounded ml-2">YOU</span> @endif</span>
                     </div>
                     <span class="text-lg font-black italic text-gray-500 tracking-tighter">{{ $s->total_score }}</span>
                </div>
            @endforeach
        </div>

        <!-- Student Actions -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-6 pt-12">
             <a href="{{ route('student.join') }}" class="btn-primary w-full md:w-auto px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-xs text-center shadow-xl">BATTLE AGAIN</a>
             <a href="{{ route('student.dashboard') }}" class="w-full md:w-auto px-12 py-5 rounded-2xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-xs text-center transition-all">DASHBOARD</a>
        </div>
    </div>
</div>
@endsection
