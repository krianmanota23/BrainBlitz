@extends('layouts.app')

@section('title', 'Admin Lobby: ' . $room->quiz->title)

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden p-8" 
     x-data="{ 
         participants: [], 
         loading: true,
         count: 0,
         max: {{ $room->quiz->max_participants }},
         roomId: {{ $room->id }},
         async fetchParticipants() {
             try {
                 const res = await fetch('{{ route('admin.rooms.participants', $room->id) }}');
                 const data = await res.json();
                 // Initialize participants from fetch, but Echo will handle updates thereafter
                 this.participants = data;
                 this.count = this.participants.length;
                 this.loading = false;
             } catch (e) { console.error(e); }
         },
         initEcho() {
            console.log('Admin Echo joining room:', this.roomId);
            window.Echo.join(`room.${this.roomId}`)
                .here((users) => {
                    console.log('Room Presence users:', users);
                    this.participants = users.filter(u => u.role === 'student');
                    this.count = this.participants.length;
                    this.loading = false;
                })
                .joining((user) => {
                    console.log('User joining:', user);
                    if (user.role === 'student') {
                        if (!this.participants.find(p => p.id === user.id)) {
                            this.participants.push(user);
                            this.count++;
                        }
                    }
                })
                .leaving((user) => {
                    console.log('User leaving:', user);
                    if (user.role === 'student') {
                        this.participants = this.participants.filter(p => p.id !== user.id);
                        this.count--;
                    }
                })
                .listen('.GameStarted', (e) => {
                    console.log('Game started event received in lobby!', e);
                    window.location.href = '{{ route('admin.game.show', $room->id) }}';
                })
                .listen('.PlayerReady', (e) => {
                    console.log('Admin Lobby PlayerReady received:', e);
                    const p = this.participants.find(user => user.id === e.user_id);
                    if (p) {
                        p.is_ready = true;
                        // Force Alpine reactivity refresh
                        this.participants = [...this.participants];
                    } else {
                        // If player not found, maybe fetch again
                        this.fetchParticipants();
                    }
                });
         }
     }" 
     x-init="fetchParticipants(); initEcho()">
    
    <!-- Top Header Lobby Info -->
    <div class="flex items-center justify-between mb-12">
        <div class="flex items-center space-x-12">
            <span class="text-4xl font-black italic tracking-tighter uppercase"><span class="text-gradient">BRAIN</span>BLITZ</span>
            <div class="h-12 w-px bg-white/10"></div>
            <div>
                <h1 class="text-3xl font-black uppercase italic tracking-tighter text-white">{{ $room->quiz->title }}</h1>
                <p class="text-gray-500 font-bold uppercase tracking-widest text-xs mt-1">Lobby is Live • Waiting for students</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
             <div class="text-right">
                <p class="text-gray-400 font-black uppercase text-[10px] tracking-widest">Players Joined</p>
                <p class="text-3xl font-black italic tracking-tighter text-white" x-text="count + ' / ' + max"></p>
             </div>
        </div>
    </div>

    <!-- HUGE ROOM CODE CENTER -->
    <div class="flex-1 flex flex-col items-center justify-center space-y-8 animate-in fade-in zoom-in duration-700">
        <div class="text-center space-y-4">
            <p class="text-gray-400 font-black uppercase tracking-[0.4em] text-sm">Join at <span class="text-white">BrainBlitz.app</span> using code:</p>
            <div class="card p-12 px-24 rounded-[3rem] shadow-[0_0_100px_rgba(168,85,247,0.2)] border-purple-500/30 relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-pink-600/5 rounded-[3rem]"></div>
                <h2 class="text-[120px] font-mono font-black tracking-[0.2em] text-white leading-none group-hover:scale-105 transition-transform duration-500 drop-shadow-[0_0_20px_rgba(168,85,247,0.5)]">
                    {{ $room->room_code }}
                </h2>
            </div>
            <p class="text-purple-400 font-bold uppercase tracking-widest text-lg animate-pulse mt-8 italic">Waiting for the squad to assemble...</p>
        </div>

        <!-- Participant Grid -->
        <div class="w-full max-w-5xl mt-12 group">
             <div x-show="participants.length === 0 && !loading" class="text-center text-gray-500 font-black uppercase tracking-widest italic animate-pulse">
                Connect your devices to enter the arena...
             </div>

             <div class="flex flex-wrap justify-center gap-4">
                <template x-for="p in participants" :key="p.id">
                    <div class="card px-6 py-4 rounded-2xl border transition-all duration-500 shadow-xl flex items-center space-x-3 animate-in slide-in-from-bottom"
                         :class="p.is_ready ? 'bg-green-500/10 border-green-500/30 text-green-400' : 'bg-white/5 border-white/5 text-white'">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black italic text-white" x-text="p.nickname.charAt(0)"></div>
                        <span class="text-xl font-black italic tracking-tighter uppercase" x-text="p.nickname"></span>
                        <template x-if="p.is_ready">
                            <span class="ml-2 text-green-500 font-bold scale-125">✓</span>
                        </template>
                    </div>
                </template>
             </div>
        </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="mt-8 flex items-center justify-between border-t border-white/10 pt-8">
        <div class="flex space-x-6">
            <form action="{{ route('admin.quizzes.index') }}" method="GET">
                <button type="submit" class="px-8 py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-xs transition-all">Cancel Room</button>
            </form>
            <a href="{{ route('tv.lobby', $room->id) }}" target="_blank" class="px-8 py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-xs transition-all flex items-center">
                 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                 Open TV Lobby
            </a>
        </div>

        <div class="flex items-center space-x-6">
             <p class="text-gray-400 text-sm font-bold opacity-0 transition-opacity duration-300" :class="count < 2 ? 'opacity-100' : ''">Need at least 2 players to start</p>
             <form action="{{ route('admin.rooms.start', $room->id) }}" method="POST">
                @csrf
                <button type="submit" 
                        :disabled="count < 2"
                        :class="count >= 2 ? 'bg-green-600 hover:bg-green-500 shadow-green-500/20' : 'bg-gray-800 opacity-50 grayscale'"
                        class="px-12 py-5 rounded-2xl font-black text-2xl tracking-tighter uppercase shadow-2xl transition-all">
                    Launch The Blitz
                </button>
             </form>
        </div>
    </div>
</div>
@endsection
