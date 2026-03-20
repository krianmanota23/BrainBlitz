<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLITZ ARENA: {{ $room->quiz->title }} - BrainBlitz</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; overflow: hidden; background: #0a0a14; }
        .text-gradient { background: linear-gradient(to bottom right, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stars { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; opacity: 0.3; }
        .star { position: absolute; background: white; border-radius: 50%; animation: blink 3s infinite; }
        @keyframes blink { 0%, 100% { opacity: 0.2; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.2); } }
        
        .timer-pulse { animation: heart-beat 1s infinite; }
        @keyframes heart-beat { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        
        .correct-reveal { animation: reveal-check 0.5s ease-out forwards; }
        @keyframes reveal-check { 0% { transform: scale(0.5); opacity: 0; } 100% { transform: scale(1.1); opacity: 1; } }

        .scoreboard-row { animation: slide-in-row 0.5s ease-out forwards; }
        @keyframes slide-in-row { 0% { transform: translateX(100vw); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body class="text-white">
    <div id="particles" class="stars"></div>

    <div class="relative z-10 w-screen h-screen flex flex-col p-12 overflow-hidden" 
         x-data="tvEngine()" 
         x-init="init()">
        
        <!-- HEADER (10%) -->
        <div class="h-[10vh] flex items-center justify-between border-b border-white/10 pb-6">
            <div class="flex items-center space-x-4">
                 <span class="text-3xl font-black italic text-purple-500">Q <span x-text="currentQuestionNum"></span> / <span x-text="totalQuestions"></span></span>
                 <div class="h-8 w-px bg-white/10 mx-4"></div>
                 <h1 class="text-4xl font-black uppercase italic tracking-tighter text-white">{{ $room->quiz->title }}</h1>
            </div>
            
            <div class="flex items-center space-x-8">
                 <div class="text-right">
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Blitz Command</p>
                    <p class="text-xs font-black italic text-gray-400">Assumption College of Davao</p>
                 </div>
                 <div class="w-24 h-24 rounded-full border-4 border-white/10 flex items-center justify-center relative"
                      :class="timer < 5 && timer > 0 ? 'border-red-500 bg-red-500/10 timer-pulse' : ''">
                    <span class="text-4xl font-black italic tabular-nums" :class="timer < 5 ? 'text-red-500' : 'text-white'" x-text="timer"></span>
                 </div>
            </div>
        </div>

        <!-- STATE 1: GET READY -->
        <div class="flex-1 flex flex-col items-center justify-center space-y-12" x-show="state == 'get_ready'">
            <h2 class="text-[15vw] font-black uppercase text-gradient italic tracking-tighter leading-none pulse-glow">GET READY!</h2>
            <p class="text-3xl font-bold uppercase tracking-[0.5em] text-gray-500">CHALLENGE <span x-text="currentQuestionNum"></span> INITIALIZING...</p>
        </div>

        <!-- STATE 2 & 3: QUESTION & OPTION (MAIN ARENA) -->
        <div class="flex-1 flex flex-col" x-show="state == 'question' || state == 'reveal'">
            <!-- Question text (35%) -->
            <div class="h-[35vh] flex flex-col items-center justify-center text-center px-24 space-y-6">
                <span class="bg-purple-600/30 text-purple-400 px-6 py-2 rounded-xl text-sm font-black uppercase tracking-widest border border-purple-500/20" x-text="topicName"></span>
                <h2 class="text-5xl lg:text-7xl font-black uppercase italic tracking-tighter text-white leading-tight" 
                    id="question-text" 
                    :class="questionText.length > 100 ? 'text-4xl lg:text-5xl' : ''"
                    x-text="questionText"></h2>
            </div>

            <!-- Options (45%) -->
            <div class="h-[45vh] grid grid-cols-2 gap-8 p-8 relative">
                <template x-for="opt in options" :key="opt.id">
                    <div class="relative rounded-[3rem] p-8 flex flex-col items-center justify-center space-y-6 border-4 shadow-2xl transition-all duration-500 overflow-hidden group"
                         :class="{
                             'bg-red-500/80 border-red-400 z-10': opt.color == 'red',
                             'bg-blue-500/80 border-blue-400 z-10': opt.color == 'blue',
                             'bg-yellow-500/80 border-yellow-400 z-10': opt.color == 'yellow',
                             'bg-green-500/80 border-green-400 z-10': opt.color == 'green',
                             'scale-[1.05] ring-[20px] ring-white !opacity-100 z-[20] shadow-[0_0_80px_#fff]': state == 'reveal' && opt.color == correctColor,
                             'opacity-20 backdrop-blur-3xl grayscale grayscale-opacity scale-95': state == 'reveal' && opt.color != correctColor
                         }">
                        
                        <div class="absolute top-8 left-8 w-16 h-16 rounded-full border-8 border-white/20 flex items-center justify-center font-black text-white text-5xl"
                             x-text="opt.color == 'red' ? '▲' : (opt.color == 'blue' ? '●' : (opt.color == 'yellow' ? '■' : '◆'))"></div>

                        <span class="text-3xl lg:text-5xl font-black uppercase italic tracking-tighter text-white drop-shadow-lg text-center px-12" x-text="opt.option_text"></span>
                        
                        <!-- Vote Progress Bar -->
                        <div class="absolute bottom-0 left-0 w-full h-[15%] bg-black/30 flex items-center px-8 border-t border-white/10 group-hover:bg-black/50 transition-all">
                             <div class="absolute bottom-0 left-0 h-full bg-white/20 transition-all duration-700"
                                  :style="`width: ${Math.round((votes[opt.color] / (participantCount || 1)) * 100)}%`"></div>
                             <div class="relative z-10 w-full flex justify-between items-center h-full">
                                <span class="text-lg font-black italic tracking-tighter tabular-nums" x-text="`${Math.round((votes[opt.color] / (participantCount || 1)) * 100)}% BLITZED`"></span>
                                <span class="text-2xl font-black italic tabular-nums text-white" x-text="votes[opt.color]"></span>
                             </div>
                        </div>

                        <!-- Icon Overlay on Reveal -->
                        <div x-show="state == 'reveal' && opt.color == correctColor" 
                             class="absolute inset-0 flex items-center justify-center pointer-events-none correct-reveal">
                             <div class="w-48 h-48 rounded-full bg-white/20 backdrop-blur-md border-[20px] border-white flex items-center justify-center text-[150px]">🏆</div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Stats (10%) -->
            <div class="h-[10vh] flex items-center justify-center">
                 <div class="flex items-center space-x-12">
                     <div class="flex items-center space-x-4">
                         <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Squad Strength</span>
                         <p class="text-3xl font-black italic tracking-tighter"><span x-text="totalAnswered"></span> / <span x-text="participantCount"></span> ACTIVE</p>
                     </div>
                     <div class="w-64 h-3 bg-white/5 rounded-full overflow-hidden border border-white/10">
                        <div class="h-full bg-purple-500 transition-all duration-300" 
                             :style="`width: ${(totalAnswered / (participantCount || 1)) * 100}%`"></div>
                     </div>
                 </div>
            </div>
        </div>

        <!-- STATE 4: SCOREBOARD -->
        <div class="flex-1 flex flex-col items-center justify-start py-8 space-y-12 h-full relative" x-show="state == 'scoreboard'">
            <div class="text-center">
                <h3 class="text-purple-500 font-black uppercase tracking-[0.5em] text-sm animate-pulse">POST-CHALLENGE STATUS</h3>
                <h2 class="text-8xl font-black italic tracking-tighter uppercase text-gradient">LEADERBOARD</h2>
            </div>

            <div class="w-full max-w-6xl space-y-4 px-12 pb-24 h-[60vh] overflow-hidden">
                <template x-for="(s, index) in scoreboard" :key="index">
                    <div class="card p-4 lg:p-6 rounded-3xl border-white/10 flex items-center justify-between group hover:border-purple-500/50 shadow-2xl scoreboard-row"
                         :style="`animation-delay: ${index * 100}ms`"
                         :class="{
                            'bg-yellow-500/20 border-yellow-500/50': s.rank == 1,
                            'bg-gray-500/10 border-gray-500/50': s.rank == 2,
                            'bg-amber-900/10 border-amber-900/50': s.rank == 3
                         }">
                        <div class="flex items-center space-x-12">
                            <span class="text-5xl lg:text-7xl font-black italic tracking-tighter min-w-[3rem] text-center"
                                  :class="s.rank == 1 ? 'text-[#FFD700]' : (s.rank == 2 ? 'text-[#C0C0C0]' : (s.rank == 3 ? 'text-[#CD7F32]' : 'text-gray-500'))"
                                  x-text="s.rank"></span>
                            <div class="h-16 w-px bg-white/10"></div>
                            <div class="flex flex-col">
                                <span class="text-3xl lg:text-5xl font-black uppercase italic tracking-tighter text-white" x-text="s.nickname"></span>
                                <div class="flex items-center space-x-4 mt-1">
                                    <template x-if="s.rank == 1">
                                        <span class="bg-yellow-500/20 text-yellow-500 text-[10px] font-black uppercase px-2 py-0.5 rounded border border-yellow-500/20">👑 CURRENT BLAZE</span>
                                    </template>
                                    <span class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">SQUAD MEMBER</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-5xl lg:text-7xl font-black italic tracking-tighter text-purple-400 group-hover:text-pink-400 tabular-nums" x-text="s.total_score"></span>
                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-[0.3em] mt-1">TOTAL PERFORMANCE PTS</p>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="absolute bottom-8 left-0 w-full text-center">
                 <p class="text-[10px] font-black uppercase tracking-[0.5em] text-gray-500 italic animate-pulse">Syncing with Battle Command... Preparing Next Challenge...</p>
            </div>
        </div>
    </div>

    <script>
        function tvEngine() {
            return {
                roomId: {{ $room->id }},
                state: 'get_ready', // get_ready, question, reveal, scoreboard
                timer: 0,
                timerInterval: null,
                
                currentQuestionNum: {{ $room->current_question }},
                totalQuestions: {{ $room->quiz->questions->count() }},
                questionText: '',
                topicName: '',
                options: [],
                votes: { red: 0, blue: 0, yellow: 0, green: 0, total: 0 },
                totalAnswered: 0,
                participantCount: 0,
                
                correctColor: null,
                scoreboard: [],

                init() {
                    this.genStars();
                    
                    Echo.join(`room.${this.roomId}`)
                        .here((users) => {
                            this.participantCount = users.filter(u => u.role === 'student').length;
                        })
                        .joining((user) => {
                            if (user.role === 'student') this.participantCount++;
                        })
                        .leaving((user) => {
                            if (user.role === 'student') this.participantCount--;
                        })
                        .listen('QuestionStarted', (e) => {
                            this.currentQuestionNum = e.question_number;
                            this.questionText = e.question.question_text;
                            this.topicName = e.question.topic?.name || 'ARENA CLASSIC';
                            this.options = e.question.options;
                            this.votes = { red: 0, blue: 0, yellow: 0, green: 0, total: 0 };
                            this.totalAnswered = 0;
                            this.state = 'question';
                            this.startTimer(e.time_limit);
                        })
                        .listen('AnswerReceived', (e) => {
                            this.votes = e.color_counts;
                            this.totalAnswered = e.color_counts.total;
                        })
                        .listen('QuestionEnded', (e) => {
                            clearInterval(this.timerInterval);
                            this.correctColor = e.correct_color;
                            this.state = 'reveal';
                            this.scoreboard = e.scoreboard;
                            
                            setTimeout(() => {
                                if (this.state === 'reveal') {
                                    this.state = 'scoreboard';
                                }
                            }, 4000);
                        })
                        .listen('GameFinished', (e) => {
                            window.location.href = `/tv/${this.roomId}/results`;
                        });
                },

                startTimer(limit) {
                    this.timer = limit;
                    clearInterval(this.timerInterval);
                    this.timerInterval = setInterval(() => {
                        if(this.timer > 0) this.timer--;
                        else clearInterval(this.timerInterval);
                    }, 1000);
                },

                genStars() {
                    const stars = document.getElementById('particles');
                    for (let i = 0; i < 60; i++) {
                        const star = document.createElement('div');
                        star.className = 'star';
                        star.style.left = Math.random() * 100 + '%';
                        star.style.top = Math.random() * 100 + '%';
                        star.style.width = star.style.height = Math.random() * 2 + 'px';
                        star.style.animationDelay = Math.random() * 5 + 's';
                        stars.appendChild(star);
                    }
                }
            }
        }
    </script>
</body>
</html>
