@extends('layouts.app')

@section('title', 'LIVE BLITZ: ' . $room->quiz->title)

@section('content')
<div class="flex h-screen overflow-hidden bg-[#0a0a14]" 
     x-data="gameEngine()" 
     x-init="initEcho()">

    <!-- LEFT SIDE: TV DISPLAY AREA (70%) -->
    <div class="w-[70%] flex flex-col relative border-r border-white/5">
        
        <!-- Top Info -->
        <div class="p-8 flex items-center justify-between">
            <span class="text-3xl font-black italic tracking-tighter uppercase"><span class="text-gradient">BRAIN</span>BLITZ</span>
            <div class="flex items-center space-x-8">
                 <div class="bg-white/5 px-6 py-2 rounded-xl border border-white/10">
                    <span class="text-gray-500 font-black uppercase tracking-widest text-[10px] block">Sequence</span>
                    <span class="text-xl font-black italic tracking-tighter text-white" x-text="`QUESTION ${currentQuestionNum} OF ${totalQuestions}`"></span>
                 </div>
                 <div class="bg-white/5 px-6 py-2 rounded-xl border border-white/10 w-32 text-center" :class="timer < 5 && timerState == 'active' ? 'border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.3)]' : ''">
                    <span class="text-gray-500 font-black uppercase tracking-widest text-[10px] block">Buffer</span>
                    <span class="text-2xl font-black italic tracking-tighter text-white" x-text="timer + 's'"></span>
                 </div>
            </div>
        </div>

        <!-- Question View -->
        <div class="flex-1 flex flex-col items-center justify-center p-12 space-y-16 animate-in fade-in zoom-in duration-1000" x-show="viewState == 'question' || viewState == 'ended'">
            <h2 class="text-6xl font-black text-center leading-tight tracking-tighter uppercase italic max-w-5xl" 
                x-text="currentQuestion.question_text"></h2>

            <!-- 2x2 Grid of options -->
            <div class="grid grid-cols-2 gap-8 w-full max-w-6xl">
                <template x-for="(opt, index) in currentQuestion.options" :key="opt.id">
                    <div class="relative h-48 rounded-3xl overflow-hidden shadow-2xl transition-all duration-500 transform border-4"
                         :class="{
                            'border-red-500': opt.color == 'red',
                            'border-blue-500': opt.color == 'blue',
                            'border-yellow-500': opt.color == 'yellow',
                            'border-green-500': opt.color == 'green',
                            'opacity-20 grayscale scale-95': viewState == 'ended' && opt.color != correctColor,
                            'scale-105 shadow-[0_0_50px_rgba(34,197,94,0.4)] z-10': viewState == 'ended' && opt.color == correctColor
                         }">
                        <!-- Background with progress bar -->
                        <div class="absolute inset-0 opacity-20"
                             :class="{
                                'bg-red-500': opt.color == 'red',
                                'bg-blue-500': opt.color == 'blue',
                                'bg-yellow-500': opt.color == 'yellow',
                                'bg-green-500': opt.color == 'green'
                             }"></div>
                        
                        <!-- Vote Progress Bar -->
                        <div class="absolute bottom-0 left-0 h-4 transition-all duration-1000"
                             :style="`width: ${calculateBarWidth(opt.color)}%;`"
                             :class="{
                                'bg-red-500': opt.color == 'red',
                                'bg-blue-500': opt.color == 'blue',
                                'bg-yellow-500': opt.color == 'yellow',
                                'bg-green-500': opt.color == 'green'
                             }"></div>

                        <div class="relative h-full flex items-center p-8">
                             <div class="w-12 h-12 rounded-full flex items-center justify-center font-black text-2xl mr-6"
                                  :class="{
                                    'bg-red-500 shadow-red-500/50': opt.color == 'red',
                                    'bg-blue-500 shadow-blue-500/50': opt.color == 'blue',
                                    'bg-yellow-500 shadow-yellow-500/50': opt.color == 'yellow',
                                    'bg-green-500 shadow-green-500/50': opt.color == 'green'
                                  }" x-text="index == 0 ? '▲' : (index == 1 ? '●' : (index == 2 ? '■' : '◆'))"></div>
                             <span class="text-3xl font-black uppercase italic tracking-tighter text-white" x-text="opt.option_text"></span>
                             
                             <div class="ml-auto text-4xl font-black italic tracking-tighter text-white/50" x-text="votes[opt.color]"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Waiting for next question -->
        <div class="flex-1 flex flex-col items-center justify-center space-y-8" x-show="viewState == 'waiting'">
            <div class="w-24 h-24 border-8 border-purple-500/20 border-t-purple-500 rounded-full animate-spin"></div>
            <h2 class="text-4xl font-black uppercase text-gradient italic tracking-tighter">Initializing Question...</h2>
        </div>

        <!-- SCOREBOARD OVERLAY -->
        <div class="absolute inset-0 bg-black/95 backdrop-blur-3xl z-[100] p-24 flex flex-col space-y-12 animate-in slide-in-from-top duration-700"
             x-show="showScoreboard" x-transition>
            <div class="text-center space-y-4">
                <h3 class="text-purple-500 font-black uppercase tracking-[0.5em] text-sm animate-pulse">QUESTION <span x-text="currentQuestionNum"></span> COMPLETE!</h3>
                <h2 class="text-8xl font-black italic tracking-tighter uppercase text-gradient">LEADERBOARD</h2>
            </div>

            <!-- Correct Answer Reveal Banner -->
            <div class="max-w-4xl mx-auto w-full p-8 rounded-[2rem] border-4 flex items-center justify-between"
                 :class="{
                    'bg-red-500/10 border-red-500': correctColor == 'red',
                    'bg-blue-500/10 border-blue-500': correctColor == 'blue',
                    'bg-yellow-500/10 border-yellow-500': correctColor == 'yellow',
                    'bg-green-500/10 border-green-500': correctColor == 'green'
                 }">
                 <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center font-black text-3xl"
                         :class="{
                            'bg-red-500': correctColor == 'red',
                            'bg-blue-500': correctColor == 'blue',
                            'bg-yellow-500': correctColor == 'yellow',
                            'bg-green-500': correctColor == 'green'
                         }" x-text="correctColor == 'red' ? '▲' : (correctColor == 'blue' ? '●' : (correctColor == 'yellow' ? '■' : '◆'))"></div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/50">Correct Answer Revealed</p>
                        <p class="text-4xl font-black uppercase italic tracking-tighter text-white" x-text="currentQuestion.options.find(o => o.color == correctColor)?.option_text"></p>
                    </div>
                 </div>
                 <div class="text-right">
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/50">Blitz Accuracy</p>
                    <p class="text-4xl font-black italic text-white" x-text="Math.round((votes[correctColor] / totalParticipants) * 100) + '%'"></p>
                 </div>
            </div>

            <div class="w-full max-w-4xl mx-auto space-y-4 pt-8">
                <template x-for="(s, index) in scoreboard" :key="index">
                    <div class="card p-6 rounded-2xl border-white/10 flex items-center justify-between group hover:border-purple-500/50 transition-all transform hover:scale-[1.02] duration-300">
                        <div class="flex items-center space-x-6">
                            <span class="text-4xl font-black italic tracking-tighter w-14"
                                  :class="s.rank == 1 ? 'text-[#FFD700]' : (s.rank == 2 ? 'text-[#C0C0C0]' : (s.rank == 3 ? 'text-[#CD7F32]' : 'text-gray-500'))"
                                  x-text="s.rank"></span>
                            <div>
                                <span class="text-3xl font-black uppercase italic tracking-tighter text-white" x-text="s.nickname"></span>
                                <p class="text-[8px] font-bold text-gray-600 uppercase tracking-widest">Connected Student</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black italic tracking-tighter text-purple-400 group-hover:text-pink-400 transition-colors" x-text="s.total_score"></span>
                            <p class="text-[8px] font-bold text-gray-600 uppercase tracking-widest">Total Blitz Pts</p>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="text-center pt-12">
                 <p class="text-gray-500 font-black uppercase tracking-[0.3em] text-[10px] italic animate-pulse">Get ready! Synchronization in progress...</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: ADMIN CONTROL AREA (30%) -->
    <div class="w-[30%] bg-white/5 p-6 flex flex-col justify-between overflow-y-auto">
        <div class="space-y-4">
            <div>
                <h3 class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-2">Command Center</h3>
                <div class="card p-4 rounded-2xl border-white/10 space-y-3 shadow-xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Responses</p>
                            <p class="text-2xl font-black italic tracking-tighter text-white"><span x-text="votes.total"></span> <span class="text-gray-500 text-sm">/ <span x-text="totalParticipants"></span></span></p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 bg-green-500/10 text-green-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-green-500/30">Stable</span>
                        </div>
                    </div>
                    
                    <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-full transition-all duration-500"
                             :style="`width: ${(votes.total / totalParticipants) * 100}%` "></div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Controls -->
            <div class="space-y-4">
                <a href="{{ route('tv.game', $room->id) }}" target="_blank" class="w-full py-3 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-[10px] transition-all flex items-center justify-center text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Open TV Display
                </a>

                <!-- Ready State Controls (Before first question) -->
                <template x-if="viewState == 'ready_check' && currentQuestionNum == 0">
                    <div class="space-y-4 animate-in fade-in zoom-in duration-500">
                        <div class="card p-4 rounded-2xl border-white/10 bg-white/5 text-center shadow-lg">
                            <p class="text-gray-400 font-black uppercase tracking-widest text-[9px] mb-1 italic">Synchronization Status</p>
                            <div class="flex items-center justify-center space-x-2">
                                <span id="ready-count" class="text-4xl font-black italic tracking-tighter text-white drop-shadow-md">
                                   0 / {{ $room->participants->count() }}
                                </span>
                                <span class="text-xs font-black uppercase tracking-widest text-purple-400">READY</span>
                            </div>
                        </div>

                        <button 
                            id="start-first-question-btn"
                            @click="startFirstQuestion()"
                            disabled
                            class="opacity-50 w-full py-4 rounded-xl font-black text-xl tracking-tighter uppercase shadow-xl transition-all duration-300 border-2 border-transparent
                                   bg-gray-800 text-gray-500 cursor-not-allowed">
                            WAITING FOR SQUAD...
                        </button>

                        <button @click="if(confirm('Not all players are ready. Force start anyway?')) startFirstQuestion()" 
                                class="w-full py-3 rounded-xl bg-red-600/20 hover:bg-red-600 border border-red-500/50 text-red-400 hover:text-white font-black uppercase tracking-widest text-xs shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>⚠️ FORCE START SYSTEM</span>
                        </button>
                    </div>
                </template>

            <template x-if="(viewState == 'waiting' || viewState == 'ended') && currentQuestionNum > 0">
                <button @click="nextQuestion()" 
                        :disabled="currentQuestionNum >= totalQuestions"
                        class="w-full py-6 rounded-2xl font-black text-2xl tracking-tighter uppercase shadow-2xl transition-all
                               bg-purple-600 hover:bg-purple-500 disabled:opacity-30">
                    Next Question &rarr;
                </button>
            </template>

            <template x-if="viewState == 'active'">
                <button @click="endQuestion()" 
                        class="w-full py-6 rounded-2xl font-black text-2xl tracking-tighter uppercase shadow-2xl transition-all
                               bg-pink-600 hover:bg-pink-500">
                    Reveal Answer &check;
                </button>
            </template>

            <template x-if="viewState == 'ended' && currentQuestionNum >= totalQuestions">
                <button @click="endGame()" 
                        class="w-full py-6 rounded-2xl font-black text-2xl tracking-tighter uppercase shadow-2xl transition-all
                               bg-red-600 hover:bg-red-500">
                    Finalize Blitz
                </button>
            </template>

            <template x-if="viewState == 'ended' && currentQuestionNum > 0">
                <button @click="showScoreboard = !showScoreboard" 
                        class="w-full py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-xs transition-all">
                    Toggle Scoreboard
                </button>
            </template>
        </div>

        <!-- Participants List Mini -->
        <div class="mt-12 pt-12 border-t border-white/10">
             <div class="flex items-center justify-between mb-6">
                <h3 class="text-gray-500 font-black uppercase tracking-widest text-[10px] italic">Connected Battleground</h3>
                <span class="text-[9px] font-black uppercase tracking-widest text-gray-600" x-text="readyCount + ' / ' + totalParticipants + ' SYNCED'"></span>
             </div>
             <div class="flex flex-wrap gap-3 overflow-y-auto max-h-48 scrollbar-hide">
                @foreach($room->participants as $participant)
                    @php 
                        $isReady = (bool)$participant->is_ready;
                    @endphp
                    <div id="participant-{{ $participant->user_id }}" 
                         data-user-id="{{ $participant->user_id }}" 
                         class="participant-badge px-5 py-3 rounded-2xl border transition-all duration-500 flex items-center space-x-3 group
                                {{ $isReady ? 'bg-green-500/10 border-green-500/30 text-green-400 shadow-[0_0_15px_rgba(34,197,94,0.1)]' : 'bg-white/5 border-white/5 text-gray-500 shadow-none' }}">
                        <div class="w-2 h-2 rounded-full transition-colors duration-500 {{ $isReady ? 'bg-green-500 animate-pulse' : 'bg-gray-700' }} ready-indicator"></div>
                        <span class="text-xs font-black uppercase tracking-tighter italic">{{ $participant->user->nickname }}</span>
                        @if($isReady)<span class="ready-check text-[10px] ml-1">✓</span>@endif
                    </div>
                @endforeach
             </div>
        </div>

        <div class="mt-auto flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-gray-600">
             <span>ROOM: {{ $room->room_code }}</span>
             <span>PHASE 06 ACTIVE</span>
        </div>
    </div>
</div>

<script>
const initialParticipants = {!! json_encode($room->participants->map(function($p) {
    return [
        'id' => $p->user_id,
        'nickname' => $p->user->nickname,
        'is_ready' => (bool)$p->is_ready,
        'role' => 'student'
    ];
})) !!};
const initialReadyCount = {{ $room->participants->where('is_ready', true)->count() }};
const initialTotalCount = {{ $room->participants->count() }};

const roomId = {{ $room->id }};

function startFirstQuestion() {
    fetch('{{ route('admin.rooms.start_first', $room->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('start-first-question-btn').textContent = 'QUESTION STARTED!';
        }
    })
    .catch(err => console.error(err));
}

function initReadyListener() {
    // Initial UI Setup for Start Button if already all ready
    const startBtn = document.getElementById('start-first-question-btn');
    const allReadyMsg = document.getElementById('all-ready-msg');
    const countEl = document.getElementById('ready-count');

    if (countEl) {
        countEl.textContent = `${initialReadyCount} / ${initialTotalCount} READY`;
    }

    if (initialReadyCount >= initialTotalCount && initialTotalCount > 0) {
        if (startBtn) {
            startBtn.disabled = false;
            startBtn.classList.remove('opacity-50', 'bg-gray-600', 'cursor-not-allowed');
            startBtn.classList.add('animate-pulse', 'bg-green-500', 'hover:bg-green-400');
            startBtn.textContent = 'ALL READY - START NOW!';
        }
        if (allReadyMsg) allReadyMsg.classList.remove('hidden');
    }

    window.Echo.join('room.' + roomId)
        .listen('.PlayerReady', (data) => {
            const badge = document.getElementById('participant-' + data.user_id);
            if (badge) {
                badge.style.borderColor = '#22c55e';
                badge.style.color = '#22c55e';
                badge.style.backgroundColor = 'rgba(34,197,94,0.15)';
                if (!badge.querySelector('.ready-check')) {
                    badge.insertAdjacentHTML('afterbegin', '<span class="ready-check">✓ </span>');
                }
            }
            
            if (countEl) {
                countEl.textContent = data.ready_count + ' / ' + data.total_count + ' READY';
            }
            
            if (data.all_ready) {
                if (startBtn) {
                    startBtn.disabled = false;
                    startBtn.classList.remove('opacity-50', 'bg-gray-600', 'cursor-not-allowed');
                    startBtn.classList.add('animate-pulse', 'bg-green-500', 'hover:bg-green-400');
                    startBtn.textContent = 'ALL READY - START NOW!';
                }
                if (allReadyMsg) allReadyMsg.classList.remove('hidden');
            }
        });
}

document.addEventListener('DOMContentLoaded', initReadyListener);

function gameEngine() {
    return {
        roomId: {{ $room->id }},
        viewState: '{{ $room->current_question == 0 ? 'ready_check' : 'waiting' }}', 
        timer: 0,
        timerInterval: null,
        currentQuestionNum: {{ $room->current_question }},
        totalQuestions: {{ $room->quiz->questions->count() }},
        currentQuestion: {!! json_encode($currentQuestion) !!} || {},
        votes: {!! json_encode($votes) !!},
        scoreboard: {!! json_encode($scoreboard) !!},
        participants: initialParticipants,
        totalParticipants: initialTotalCount,
        readyCount: initialReadyCount,
        allReady: (initialReadyCount === initialTotalCount && initialTotalCount > 0),
        showScoreboard: false,
        correctColor: null,

        async startFirstQuestion() {
            try {
                const res = await fetch(`{{ route('admin.rooms.start_first', $room->id) }}`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('First question start failed');
                const data = await res.json();
                if(data.success) {
                    this.currentQuestion = data.question;
                    this.currentQuestionNum = data.question_number;
                    this.startTimer(data.question.time_limit);
                    this.viewState = 'active';
                    this.votes = { red: 0, blue: 0, yellow: 0, green: 0, total: 0 };
                }
            } catch (e) { console.error(e); }
        },

        initEcho() {
            Echo.join(`room.${this.roomId}`)
                .here((users) => {
                    this.participants = users.filter(u => u.role === 'student').map(u => ({...u, is_ready: u.is_ready || false}));
                    this.totalParticipants = this.participants.length;
                    this.readyCount = this.participants.filter(p => p.is_ready).length;
                    this.allReady = (this.readyCount === this.totalParticipants && this.totalParticipants > 0);
                })
                .joining((user) => {
                    if(user.role === 'student') {
                        if (!this.participants.find(p => p.id === user.id)) {
                            this.participants.push({...user, is_ready: false});
                            this.totalParticipants++;
                            this.allReady = false;
                        }
                    }
                })
                .leaving((user) => {
                    if(user.role === 'student') {
                        this.participants = this.participants.filter(u => u.id !== user.id);
                        this.totalParticipants = this.participants.length;
                        this.readyCount = this.participants.filter(p => p.is_ready).length;
                        this.allReady = (this.readyCount === this.totalParticipants && this.totalParticipants > 0);
                    }
                })
                .listen('.PlayerReady', (e) => {
                    const player = this.participants.find(p => p.id === e.user_id);
                    if (player) {
                        player.is_ready = true;
                        this.readyCount = e.ready_count;
                        this.totalParticipants = e.total_count;
                        this.allReady = e.all_ready;
                    }
                })
                .listen('.AnswerReceived', (e) => {
                    console.log('Vote received:', e.color_counts);
                    this.votes = e.color_counts;
                    this.votes.total = Object.values(this.votes).reduce((a, b) => a + b, 0);
                });

            if(this.currentQuestion.id) {
                this.viewState = 'ended';
            }
        },

        async nextQuestion() {
            this.viewState = 'waiting';
            this.showScoreboard = false;
            this.correctColor = null;
            
            try {
                const res = await fetch(`{{ route('admin.rooms.next', $room->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if(data.success) {
                    this.currentQuestion = data.question;
                    this.currentQuestionNum = data.question_number;
                    this.startTimer(data.question.time_limit);
                    this.viewState = 'active';
                    this.votes = { red: 0, blue: 0, yellow: 0, green: 0, total: 0 };
                }
            } catch (e) { console.error(e); }
        },

        async endQuestion() {
            clearInterval(this.timerInterval);
            try {
                const res = await fetch(`{{ route('admin.rooms.end_question', $room->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if(data.success) {
                    this.scoreboard = data.scoreboard;
                    this.correctColor = data.correct_color;
                    this.viewState = 'ended';
                    setTimeout(() => { this.showScoreboard = true; }, 2000);
                }
            } catch (e) { console.error(e); }
        },

        async endGame() {
            try {
                await fetch(`{{ route('admin.rooms.end_game', $room->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                window.location.href = '{{ route('admin.rooms.results', $room->id) }}';
            } catch (e) { console.error(e); }
        },

        startTimer(limit) {
            this.timer = limit;
            clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if(this.timer > 0) this.timer--;
                else {
                    clearInterval(this.timerInterval);
                    if (this.viewState === 'active') {
                        this.endQuestion();
                    }
                }
            }, 1000);
        },

        calculateBarWidth(color) {
            if(this.totalParticipants === 0) return 0;
            return (this.votes[color] / this.totalParticipants) * 100;
        }
    }
}
</script>
@endsection
