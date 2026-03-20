<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINALE: {{ $room->quiz->title }} - BrainBlitz</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; overflow: hidden; background: #0a0a14; }
        .text-gradient { background: linear-gradient(to bottom right, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .podium-column { animation: rise-up 2s cubic-bezier(0.17, 0.67, 0.83, 0.67) forwards; opacity: 0; }
        @keyframes rise-up { 0% { height: 0; opacity: 0; } 100% { height: var(--h); opacity: 1; } }
        
        .confetti-piece { position: absolute; border-radius: 50%; opacity: 0.8; animation: fall 4s infinite linear; pointer-events: none; }
        @keyframes fall {
            0% { transform: translateY(-10vh) rotate(0deg); }
            100% { transform: translateY(110vh) rotate(360deg); }
        }

        .highlight-pulse { animation: highlight-beat 2s infinite; }
        @keyframes highlight-beat { 0%, 100% { box-shadow: 0 0 40px rgba(255,215,0,0); } 50% { box-shadow: 0 0 100px rgba(255,215,0,0.4); } }
    </style>
</head>
<body class="text-white">
    <div id="confetti-container" class="fixed inset-0 pointer-events-none z-50 overflow-hidden"></div>

    <div class="relative z-10 w-screen h-screen flex flex-col p-24 overflow-hidden" 
         x-data="tvFinal()" 
         x-init="init()">
        
        <!-- PHASE 1: BUILD UP -->
        <div class="flex-1 flex flex-col items-center justify-center space-y-12" x-show="phase == 'buildup'" x-transition>
            <h1 class="text-[10vw] font-black italic tracking-tighter uppercase text-gradient animate-pulse">BATTLE CONCLUDED</h1>
            <p class="text-3xl font-black uppercase tracking-[0.8em] text-gray-500 italic">CALCULATING FINAL RANKINGS...</p>
        </div>

        <!-- PHASE 2: PODIUM REVEAL -->
        <div class="flex-1 flex flex-col items-center justify-end space-y-24" x-show="phase == 'podium' || phase == 'leaderboard'" x-transition>
            <div class="text-center">
                <h3 class="text-purple-500 font-black uppercase tracking-[0.5em] text-sm mb-4">THE CHAMPIONS</h3>
                <h2 class="text-7xl lg:text-9xl font-black italic tracking-tighter uppercase text-gradient">FINAL PODIUM</h2>
            </div>

            <div class="flex items-end justify-center space-x-12 w-full pb-12 max-w-7xl">
                <!-- 2ND PLACE -->
                @if($scores->count() >= 2)
                <div class="flex flex-col items-center space-y-12 w-1/4" x-show="revealedCount >= 2">
                    <div class="text-center animate-in zoom-in slide-in-from-bottom duration-700">
                        <span class="text-6xl lg:text-8xl">🥈</span>
                        <p class="text-2xl lg:text-4xl font-black uppercase italic text-white mt-4 tracking-tighter">@if($scores->count() >= 2) {{ $scores[1]->user->nickname }} @endif</p>
                        <p class="text-3xl lg:text-5xl font-black italic text-gray-500 tracking-tighter">@if($scores->count() >= 2) {{ $scores[1]->total_score }} @endif</p>
                    </div>
                    <div class="w-full bg-gradient-to-t from-gray-700 to-gray-500/20 podium-column rounded-t-[3rem] border-t-8 border-gray-400/50 shadow-2xl" 
                         style="--h: 30vh; animation-delay: 500ms"></div>
                </div>
                @endif

                <!-- 1ST PLACE -->
                @if($scores->count() >= 1)
                <div class="flex flex-col items-center space-y-12 w-1/3" x-show="revealedCount >= 3">
                    <div class="text-center relative animate-in zoom-in slide-in-from-bottom duration-1000">
                        <span class="text-8xl lg:text-[10rem] drop-shadow-[0_0_40px_rgba(255,215,0,0.6)]">👑</span>
                        <p class="text-4xl lg:text-6xl font-black uppercase italic text-white mt-8 tracking-tighter">@if($scores->count() >= 1) {{ $scores[0]->user->nickname }} @endif</p>
                        <p class="text-5xl lg:text-8xl font-black italic text-[#FFD700] drop-shadow-lg tracking-tighter">@if($scores->count() >= 1) {{ $scores[0]->total_score }} @endif</p>
                    </div>
                    <div class="w-full bg-gradient-to-t from-yellow-700 to-yellow-500/40 podium-column rounded-t-[5rem] border-t-8 border-[#FFD700] shadow-[0_0_100px_rgba(255,215,0,0.3)] highlight-pulse" 
                         style="--h: 45vh; animation-delay: 1.5s"></div>
                </div>
                @endif

                <!-- 3RD PLACE -->
                @if($scores->count() >= 3)
                <div class="flex flex-col items-center space-y-12 w-1/4" x-show="revealedCount >= 1">
                    <div class="text-center animate-in zoom-in slide-in-from-bottom duration-700">
                        <span class="text-5xl lg:text-7xl">🥉</span>
                        <p class="text-xl lg:text-3xl font-black uppercase italic text-white mt-4 tracking-tighter">@if($scores->count() >= 3) {{ $scores[2]->user->nickname }} @endif</p>
                        <p class="text-2xl lg:text-4xl font-black italic text-amber-800 tracking-tighter">@if($scores->count() >= 3) {{ $scores[2]->total_score }} @endif</p>
                    </div>
                    <div class="w-full bg-gradient-to-t from-amber-900 to-amber-700/20 podium-column rounded-t-[2.5rem] border-t-8 border-amber-800 shadow-2xl" 
                         style="--h: 22vh; animation-delay: 200ms"></div>
                </div>
                @endif
            </div>

            <!-- Leaderboard Banner -->
            <div class="pb-24 w-full flex justify-center" x-show="phase == 'leaderboard'" x-transition>
                 <p class="text-gray-500 font-black uppercase tracking-[1em] text-sm italic animate-pulse">RELOAD PAGE TO RESTART CEREMONY OR VIEW FULL LIST BELOW</p>
            </div>
        </div>
    </div>

    <script>
        function tvFinal() {
            return {
                phase: 'buildup', // buildup, podium, leaderboard
                revealedCount: 0,
                
                init() {
                    setTimeout(() => {
                        this.phase = 'podium';
                        this.startCeremony();
                    }, 4000);
                },

                startCeremony() {
                    // Reveal 3rd
                    setTimeout(() => { this.revealedCount = 1; }, 500);
                    // Reveal 2nd
                    setTimeout(() => { this.revealedCount = 2; }, 1500);
                    // Reveal 1st
                    setTimeout(() => { 
                        this.revealedCount = 3; 
                        this.triggerConfetti();
                    }, 3000);
                    
                    setTimeout(() => { this.phase = 'leaderboard'; }, 6000);
                },

                triggerConfetti() {
                    const container = document.getElementById('confetti-container');
                    const colors = ['#FFD700', '#A855F7', '#EC4899', '#FFFFFF', '#22C55E'];
                    
                    for (let i = 0; i < 150; i++) {
                        const piece = document.createElement('div');
                        piece.className = 'confetti-piece';
                        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                        piece.style.left = Math.random() * 100 + 'vw';
                        piece.style.top = -Math.random() * 20 + 'vh';
                        piece.style.width = Math.random() * 15 + 5 + 'px';
                        piece.style.height = piece.style.width;
                        piece.style.animationDelay = Math.random() * 3 + 's';
                        piece.style.animationDuration = Math.random() * 2 + 3 + 's';
                        container.appendChild(piece);
                    }
                }
            }
        }
    </script>
</body>
</html>
