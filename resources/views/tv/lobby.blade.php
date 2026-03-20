<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOBBY: {{ $room->room_code }} - BrainBlitz</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; overflow: hidden; background: #0f0f1a; }
        .text-gradient { background: linear-gradient(to bottom right, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stars { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; }
        .star { position: absolute; background: white; border-radius: 50%; animation: blink 3s infinite; }
        @keyframes blink { 0%, 100% { opacity: 0.2; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.2); } }
        .pulse-glow { animation: pulse 3s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); text-shadow: 0 0 40px rgba(168,85,247,0); } 50% { transform: scale(1.05); text-shadow: 0 0 60px rgba(168,85,247,0.5); } }
    </style>
</head>
<body class="text-white">
    <div id="particles" class="stars"></div>

    <div class="relative z-10 w-screen h-screen flex flex-col items-center justify-between p-12 text-center"
         x-data="tvLobby()" 
         x-init="init()">
        
        <!-- TOP SECTION: Branding -->
        <div class="h-[25vh] flex flex-col items-center justify-center space-y-2 animate-in fade-in slide-in-from-top duration-1000">
            <h1 class="text-3xl lg:text-5xl font-black italic tracking-tighter uppercase"><span class="text-gradient">BRAIN</span>BLITZ LIVE</h1>
            <p class="text-gray-500 font-bold tracking-[0.5em] uppercase text-sm lg:text-xl">PLAYER ENTRANCE OPEN</p>
            <p class="text-[10px] lg:text-xs font-black text-gray-700 uppercase tracking-widest mt-4">Assumption College of Davao</p>
        </div>

        <!-- MIDDLE SECTION: ROOM CODE -->
        <div class="h-[45vh] flex flex-col items-center justify-center space-y-6">
            <p class="text-gray-600 font-black uppercase tracking-[0.5em] text-sm lg:text-xl">ENTER ROOM CODE</p>
            <h2 class="text-[15vw] font-black tracking-tighter text-gradient leading-none italic pulse-glow tabular-nums select-none">{{ $room->room_code }}</h2>
            <div class="flex flex-col items-center space-y-2">
                 <p class="text-gray-400 font-black uppercase tracking-widest text-lg lg:text-3xl">Join at <span class="text-white border-b-2 border-purple-500 pb-1">brainblitz.acv.edu/join</span></p>
                 <p class="text-xs font-bold text-gray-600 uppercase tracking-[0.3em]">Ready your device and lock in nicks!</p>
            </div>
        </div>

        <!-- BOTTOM SECTION: PLAYERS -->
        <div class="h-[30vh] w-full max-w-7xl flex flex-col items-center justify-end space-y-12">
            <div class="text-center space-y-2">
                <p class="text-gray-500 font-black uppercase tracking-widest text-sm lg:text-lg">SQUAD ENLISTED</p>
                <p class="text-5xl lg:text-7xl font-black italic text-gradient tracking-tighter tabular-nums" x-text="`${count} / ${max}`"></p>
            </div>

            <!-- Participant Bubbles -->
            <div class="flex flex-wrap items-center justify-center gap-4 px-12 pb-12">
                <template x-for="p in participants" :key="p.id">
                    <div class="px-6 py-2 rounded-full bg-white/5 border border-white/10 text-xs lg:text-lg font-black uppercase tracking-widest italic animate-in zoom-in slide-in-from-bottom-2 duration-500" x-text="p.nickname"></div>
                </template>
                <div x-show="participants.length == 0" class="text-gray-700 font-black uppercase tracking-[0.3em] text-xs">Waiting for first combatants...</div>
            </div>

            <div class="pb-8">
                 <p class="text-sm font-black uppercase tracking-widest text-purple-600 animate-pulse" x-text="count >= max ? 'ARENA IS FULL!' : 'BATTLE COMMAND IN PRESET...'"></p>
            </div>
        </div>
    </div>

    <script>
        function tvLobby() {
            return {
                roomId: {{ $room->id }},
                participants: [],
                count: 0,
                max: {{ $room->quiz->max_participants }},
                
                init() {
                    this.genStars();
                    this.pollParticipants();
                    this.pollStatus();
                },

                genStars() {
                    const stars = document.getElementById('particles');
                    for (let i = 0; i < 100; i++) {
                        const star = document.createElement('div');
                        star.className = 'star';
                        star.style.left = Math.random() * 100 + '%';
                        star.style.top = Math.random() * 100 + '%';
                        star.style.width = star.style.height = Math.random() * 3 + 'px';
                        star.style.animationDelay = Math.random() * 5 + 's';
                        stars.appendChild(star);
                    }
                },

                async pollParticipants() {
                    try {
                        const res = await fetch(`/admin/rooms/${this.roomId}/participants`);
                        this.participants = await res.json();
                        this.count = this.participants.length;
                    } catch (e) {}
                    setTimeout(() => this.pollParticipants(), 3000);
                },

                async pollStatus() {
                    try {
                        const res = await fetch(`/student/rooms/${this.roomId}/status`);
                        const data = await res.json();
                        if (data.status === 'ongoing') {
                            window.location.href = `/tv/${this.roomId}/game`;
                        }
                    } catch (e) {}
                    setTimeout(() => this.pollStatus(), 2000);
                }
            }
        }
    </script>
</body>
</html>
