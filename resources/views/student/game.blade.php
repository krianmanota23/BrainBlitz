@extends('layouts.app')

@section('title', 'BATTLE ARENA')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-[#0a0a14]" 
     x-data="studentEngine()" 
     x-init="initEcho(); if (viewState === 'active' && timer > 0) startTimer(timer);">

    <!-- Header Stats -->
    <div class="px-6 py-4 flex items-center justify-between border-b border-white/5">
        <div class="flex items-center space-x-3">
             <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black italic text-xs">⚡</div>
             <span class="text-sm font-black uppercase italic tracking-tighter text-white">BLITZ ARENA</span>
        </div>
        <div class="flex items-center space-x-4">
             <div class="text-right">
                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Score</p>
                <p class="text-xs font-black italic tracking-tighter text-white" x-text="totalScore"></p>
             </div>
             <div class="h-6 w-px bg-white/10 mx-2"></div>
             <div class="text-right">
                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Rank</p>
                <p class="text-xs font-black italic tracking-tighter text-purple-400" x-text="`#${currentRank}`"></p>
             </div>
        </div>
    </div>

    <!-- STATE 1: GET READY (WAITING) -->
    <div class="flex-1 flex flex-col items-center justify-center space-y-12 p-8" x-show="viewState == 'ready'">
        <div class="relative">
            <div class="w-48 h-48 rounded-full border-4 border-purple-500/20 flex items-center justify-center animate-pulse">
                <div class="w-32 h-32 bg-purple-500/20 rounded-full animate-ping opacity-50 absolute"></div>
                <span class="text-6xl font-black italic text-gradient tracking-tighter">!</span>
            </div>
        </div>
        <div class="text-center space-y-8 w-full max-w-md">
            <div x-show="!allReady">
                <h2 class="text-5xl font-black uppercase text-white italic tracking-tighter mb-8">GET READY!</h2>
                
                <button @click="sendReady()" :disabled="isReady"
                    class="w-full py-8 rounded-2xl font-black text-2xl tracking-tighter uppercase transition-all duration-300 shadow-[0_0_50px_rgba(34,197,94,0.3)] flex items-center justify-center space-x-4"
                    :class="isReady ? 'bg-[#15803d] text-white cursor-default' : 'bg-gradient-to-b from-[#22c55e] to-[#16a34a] text-white animate-pulse hover:scale-105 active:scale-95'">
                    <span x-show="!isReady">TAP WHEN READY!</span>
                    <span x-show="isReady" class="flex items-center">✓ READY!</span>
                </button>

                <div class="mt-8 space-y-2">
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-xs italic" x-text="isReady ? 'Waiting for others...' : 'Next Challenge Initializing...'"></p>
                    <p class="text-[#22c55e] font-black text-xl tabular-nums" x-text="`${readyCount} / ${totalCount} READY`"></p>
                </div>
            </div>

            <div x-show="allReady" class="animate-bounce group">
                <h2 class="text-5xl font-black uppercase text-green-500 italic tracking-tighter mb-4 shadow-green-500/20 drop-shadow-2xl">ALL PLAYERS READY!</h2>
                <p class="text-white font-black uppercase tracking-[0.2em] text-sm italic">Waiting for host to start...</p>
            </div>
        </div>
    </div>

    <!-- STATE 2 & 3: QUESTION ACTIVE / ANSWERED -->
    <div class="flex-1 flex flex-col h-full" x-show="viewState == 'active' || viewState == 'answered'">
        <!-- Question Summary -->
        <div class="p-4 bg-white/5 text-center flex items-center justify-between">
             <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Question <span x-text="currentQuestionNum"></span></span>
             <!-- Timer -->
             <div class="px-4 py-1 rounded-full border border-white/10 flex items-center space-x-2" :class="timer < 5 ? 'border-red-500 bg-red-500/10' : ''">
                <span class="text-xs font-black italic text-white" x-text="timer + 's'"></span>
             </div>
        </div>

        <div class="p-6 text-center flex-shrink-0">
             <h2 class="text-2xl font-black uppercase tracking-tighter italic text-white leading-tight" x-text="questionText"></h2>
        </div>

        <!-- BIG BUTTONS GRID -->
        <div class="flex-1 grid grid-cols-2 gap-2 p-2 relative pb-8">
            <template x-for="opt in options" :key="opt.id">
                <button @click="submitAnswer(opt.id, opt.color)" 
                        :disabled="viewState == 'answered' || timer <= 0"
                        class="relative rounded-2xl flex flex-col items-center justify-center space-y-4 transition-all duration-300 transform active:scale-95 overflow-hidden border-b-8 border-r-4 shadow-xl"
                        :class="{
                            'bg-red-600 border-red-800 shadow-red-900/40': opt.color == 'red',
                            'bg-blue-600 border-blue-800 shadow-blue-900/40': opt.color == 'blue',
                            'bg-yellow-500 border-yellow-700 shadow-yellow-900/40': opt.color == 'yellow',
                            'bg-green-600 border-green-800 shadow-green-900/40': opt.color == 'green',
                            'opacity-20 scale-95 grayscale': viewState == 'answered' && selectedColor != opt.color,
                            'ring-8 ring-white shadow-[0_0_30px_#fff] z-10': viewState == 'answered' && selectedColor == opt.color
                        }">
                    
                    <div class="absolute top-4 left-4 w-10 h-10 rounded-full border-4 border-white/20 flex items-center justify-center font-black text-white text-2xl"
                         x-text="opt.color == 'red' ? '▲' : (opt.color == 'blue' ? '●' : (opt.color == 'yellow' ? '■' : '◆'))"></div>

                    <span class="text-2xl font-black uppercase tracking-tighter text-white drop-shadow-lg p-4" x-text="opt.option_text"></span>
                    
                    <div x-show="viewState == 'answered' && selectedColor == opt.color" class="absolute inset-0 bg-white/10 flex items-center justify-center backdrop-blur-[2px]">
                         <span class="bg-black/80 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-white animate-bounce">LOCK-IN DONE!</span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- STATE 4: RESULT / SCOREBOARD -->
    <div class="flex-1 flex flex-col items-center justify-center p-8 space-y-8 relative animate-in slide-in-from-bottom duration-500 overflow-y-auto" x-show="viewState == 'result'">
        
        <div class="text-center space-y-6">
            <div x-show="isCorrect" class="text-7xl mb-4 animate-bounce">🏆</div>
            <div x-show="!isCorrect" class="text-7xl mb-4 text-red-500">❌</div>
            
            <h2 class="text-5xl font-black uppercase italic tracking-tighter grow"
                :class="isCorrect ? 'text-green-500' : 'text-red-500'"
                x-text="isCorrect ? 'CORRECT!' : 'WRONG!'"></h2>

            <div class="card p-8 rounded-3xl border-white/10 bg-white/5 space-y-2">
                 <p class="text-gray-500 font-black uppercase tracking-widest text-[10px]">Round Battle Points</p>
                 <p class="text-6xl font-black italic tracking-tighter" :class="pointsEarned > 0 ? 'text-gradient' : 'text-gray-600'" x-text="`+${pointsEarned}`"></p>
            </div>
            
            <div class="flex items-center justify-center space-x-4">
                 <div class="bg-white/5 px-6 py-2 rounded-xl border border-white/10">
                    <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Your Rank</p>
                    <p class="text-xl font-black italic text-purple-400" x-text="`#${currentRank}`"></p>
                 </div>
                 <div class="bg-white/5 px-6 py-2 rounded-xl border border-white/10">
                    <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Total Blitz</p>
                    <p class="text-xl font-black italic text-white" x-text="totalScore"></p>
                 </div>
            </div>
        </div>

        <!-- Mini Leaderboard -->
        <div class="w-full max-w-sm space-y-2">
            <p class="text-[10px] font-black text-gray-600 uppercase tracking-[0.3em] text-center mb-4">Top Squad Standings</p>
            <template x-for="s in miniScoreboard" :key="s.nickname">
                <div class="flex items-center justify-between px-6 py-3 rounded-xl border border-white/5 bg-white/5"
                     :class="s.nickname === '{{ Auth::user()->nickname }}' ? 'border-purple-500/50 bg-purple-500/10' : ''">
                    <span class="text-sm font-black italic text-gray-500 w-6" x-text="`#${s.rank}`"></span>
                    <span class="text-sm font-black uppercase italic tracking-tighter text-white" x-text="s.nickname"></span>
                    <span class="text-sm font-black italic text-gray-500" x-text="s.total_score"></span>
                </div>
            </template>
        </div>

        <!-- Next Step Meta -->
        <div class="text-center pt-8">
             <div class="flex items-center justify-center space-x-2 animate-pulse">
                <div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
                <div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
                <div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
             </div>
             <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mt-4 italic">Next challenge coming up...</p>
        </div>
    </div>

    <!-- Background Decals -->
    <div class="fixed -bottom-32 -left-32 w-64 h-64 bg-purple-600/5 rounded-full blur-[80px] pointer-events-none"></div>
    <div class="fixed -top-32 -right-32 w-64 h-64 bg-pink-600/5 rounded-full blur-[80px] pointer-events-none"></div>
</div>

<script>
function studentEngine() {
    return {
        roomId: {{ $room->id }},
        viewState: '{{ $alreadyAnswered ? 'answered' : ($room->current_question > 0 ? 'active' : 'ready') }}',
        timer: {{ $remainingTime ?? 0 }},
        timerInterval: null,
        totalScore: 0,
        currentRank: 1,
        pointsEarned: 0,
        isCorrect: false,
        lastTotalScore: 0,
        miniScoreboard: [],
        
        isReady: false,
        readyCount: 0,
        totalCount: 0,
        allReady: false,
        
        currentQuestionNum: {{ $room->current_question }},
        questionText: '{!! addslashes($currentQuestion->question_text ?? '') !!}',
        options: {!! json_encode($currentQuestion?->options ?? []) !!},
        selectedColor: '{{ $selectedColor ?? '' }}',

        async fetchReadyCount() {
            try {
                const res = await fetch(`{{ route('student.ready.count', $room->id) }}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Ready count fetch failed');
                const data = await res.json();
                this.readyCount = data.ready_count;
                this.totalCount = data.total_count;
                this.allReady = data.all_ready;
            } catch (e) { console.error('Fetch Error:', e); }
        },

        startPolling() {
            setInterval(async () => {
                try {
                    if (this.viewState === 'ready') {
                        this.fetchReadyCount();
                    }

                    const statusRes = await fetch(`{{ route('student.rooms.status', $room->id) }}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (statusRes.ok) {
                        const statusData = await statusRes.json();
                        
                        if (statusData.status === 'finished') {
                            window.location.href = `{{ route('student.rooms.results', $room->id) }}`;
                            return;
                        }

                        if (statusData.current_question > this.currentQuestionNum) {
                            window.location.reload();
                            return;
                        }

                        if (this.viewState === 'ready' && statusData.current_question > 0) {
                            window.location.reload();
                            return;
                        }
                    }
                } catch (e) {}
            }, 2000);
        },

        async sendReady() {
            if(this.isReady) return;
            this.isReady = true;
            try {
                const res = await fetch(`{{ route('student.ready', $room->id) }}`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Mark ready failed');
                const data = await res.json();
                this.readyCount = data.ready_count;
                this.totalCount = data.total_count;
                this.allReady = data.all_ready;
            } catch (e) {
                console.error(e);
                this.isReady = false;
            }
        },

        initEcho() {
            this.fetchReadyCount();
            this.startPolling();
            console.log('Initializing Echo for Room:', this.roomId);
            window.Echo.join(`room.${this.roomId}`)
                .listen('.GameStarted', (e) => {
                    this.viewState = 'ready'; 
                })
                .listen('.PlayerReady', (e) => {
                    console.log('Real-time PlayerReady received:', e);
                    this.readyCount = e.ready_count;
                    this.totalCount = e.total_count;
                    this.allReady = e.all_ready;
                })
                .listen('.QuestionStarted', (e) => {
                    this.currentQuestionNum = e.question_number;
                    this.questionText = e.question.question_text;
                    this.options = e.question.options;
                    this.selectedColor = null;
                    this.viewState = 'active';
                    this.startTimer(e.time_limit);
                })
                .listen('.QuestionEnded', (e) => {
                    clearInterval(this.timerInterval);
                    this.miniScoreboard = e.scoreboard.slice(0, 3);
                    
                    const me = e.scoreboard.find(s => s.nickname === '{{ Auth::user()->nickname }}');
                    if(me) {
                        this.totalScore = me.total_score;
                        this.currentRank = me.rank;
                    }
                    this.isCorrect = (this.selectedColor === e.correct_color);
                    this.pointsEarned = this.isCorrect ? (this.totalScore - (this.lastTotalScore || 0)) : 0;
                    this.lastTotalScore = this.totalScore;
                    this.viewState = 'result';
                })
                .listen('.GameFinished', (e) => {
                    window.location.href = `/student/rooms/${this.roomId}/results`;
                });
        },

        async submitAnswer(optionId, color) {
            if(this.viewState !== 'active') return;
            
            this.selectedColor = color;
            this.viewState = 'answered';

            try {
                const res = await fetch(`/student/rooms/${this.roomId}/answer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ option_id: optionId })
                });
                if (!res.ok) throw new Error('Answer submission failed');
                const data = await res.json();
                if(!data.success) {
                    console.error(data.error);
                }
            } catch (e) {
                console.error(e);
            }
        },

        startTimer(limit) {
            this.timer = limit;
            clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if(this.timer > 0) this.timer--;
                else clearInterval(this.timerInterval);
            }, 1000);
        },

        calculatePoints(correctColor) {
            // This is just for UI, final points handled by server
            if(this.selectedColor === correctColor) {
                 return 1000 + Math.floor(900 * (this.timer / 30)); // Rough estimate
            }
            return 0;
        }
    }
}
</script>
@endsection
