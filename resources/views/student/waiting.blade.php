@extends('layouts.app')

@section('title', 'Lobby: ' . $room->quiz->title)

@section('content')
<div class="flex-1 flex flex-col items-center justify-center p-8 space-y-12" 
     x-data="{ 
         status: 'waiting', 
         count: {{ $room->participants->count() }},
         max: {{ $room->quiz->max_participants }},
         readyCount: 0,
         totalCount: {{ $room->participants->count() }},
         isReady: {{ isset($isParticipantReady) && $isParticipantReady ? 'true' : 'false' }},
         markingReady: false,
         async checkStatus() {
            try {
                const res = await fetch('{{ route('student.rooms.status', $room->id) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Status fetch failed');
                const data = await res.json();
                this.status = data.status;
                this.count = data.participant_count;
                
                const readyRes = await fetch('{{ route('student.ready.count', $room->id) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!readyRes.ok) throw new Error('Ready count fetch failed');
                const readyData = await readyRes.json();
                this.readyCount = readyData.ready_count;
                this.totalCount = readyData.total_count;

                if(this.status === 'ongoing') {
                    window.location.href = '{{ route('student.game', $room->id) }}';
                }
            } catch (e) { console.error(e); }
         },
         async toggleReady() {
            if (this.isReady || this.markingReady) return;
            this.markingReady = true;
            try {
                const res = await fetch('{{ route('student.ready', $room->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.isReady = true;
                    this.readyCount = data.ready_count;
                    this.totalCount = data.total_count;
                }
            } catch (e) { console.error(e); }
            finally { this.markingReady = false; }
         },
         initEcho() {
             if (window.Echo) {
                 window.Echo.join(`room.{{ $room->id }}`)
                     .listen('.PlayerReady', (e) => {
                         this.readyCount = e.readyCount;
                         this.totalCount = e.totalCount;
                     })
                     .listen('.GameStarted', (e) => {
                         window.location.href = '{{ route('student.game', $room->id) }}';
                     });
             }
         }
     }" 
     x-init="checkStatus(); initEcho(); setInterval(() => checkStatus(), 3000)">
    
    <!-- Top BrainBlitz Logo -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-black italic tracking-tighter uppercase"><span class="text-gradient">BRAIN</span>BLITZ</h1>
    </div>

    <!-- Centered Waiting Content -->
    <div class="card p-10 md:p-14 rounded-[3.5rem] w-full max-w-2xl text-center space-y-8 shadow-[0_0_80px_rgba(168,85,247,0.15)] border-white/10 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-pink-600/10 opacity-50"></div>
        
        <div class="relative space-y-6 animate-in fade-in zoom-in duration-700">
            <!-- Pulsing Loading Indicator -->
            <div class="w-28 h-28 bg-purple-600/20 rounded-full mx-auto flex items-center justify-center border border-white/20 animate-pulse relative">
                <div class="w-20 h-20 bg-purple-500/40 rounded-full animate-ping opacity-50"></div>
                <div class="absolute w-14 h-14 bg-purple-500 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(168,85,247,0.8)]">
                     <span class="text-white text-2xl font-black uppercase tracking-tighter italic">⚡</span>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-4xl md:text-5xl font-black uppercase text-gradient italic tracking-tighter animate-pulse">WAITING FOR HOST...</h2>
                <h3 class="text-2xl font-black uppercase tracking-tighter text-white">{{ $room->quiz->title }}</h3>
                <p class="text-gray-400 font-black uppercase tracking-widest text-xs italic">Arena Code: <span class="text-purple-400 font-mono text-base">{{ $room->room_code }}</span></p>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white/5 py-3 px-6 border border-white/10 rounded-2xl flex flex-col items-center justify-center">
                    <span class="text-gray-400 font-black uppercase tracking-widest text-[9px] mb-1">Squad Size</span>
                    <span class="text-white font-black italic text-lg" x-text="count + ' / ' + max"></span>
                </div>
                
                <div class="py-3 px-6 border border-white/10 rounded-2xl flex flex-col items-center justify-center transition-all"
                     :class="readyCount === totalCount && totalCount > 0 ? 'bg-green-500/10 border-green-500/30' : 'bg-white/5 border-white/10'">
                    <span class="text-gray-400 font-black uppercase tracking-widest text-[9px] mb-1">Ready Count</span>
                    <span class="font-black italic text-lg" 
                          :class="readyCount === totalCount && totalCount > 0 ? 'text-green-400' : 'text-purple-400'"
                          x-text="readyCount + ' / ' + totalCount"></span>
                </div>
            </div>

            <!-- Interactive Ready Up Button -->
            <div class="pt-4">
                <button @click="toggleReady()" 
                        :disabled="isReady || markingReady"
                        :class="isReady ? 'bg-green-600 border-green-400 text-white cursor-default shadow-[0_0_40px_rgba(34,197,94,0.4)]' : 'bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white shadow-[0_0_50px_rgba(168,85,247,0.4)] hover:scale-105 active:scale-95 cursor-pointer'"
                        class="w-full py-5 rounded-2xl font-black text-2xl tracking-tighter uppercase border transition-all duration-300 transform flex items-center justify-center space-x-3 select-none">
                    <span x-show="!isReady && !markingReady">⚡ I'M READY!</span>
                    <span x-show="markingReady" style="display:none;" class="flex items-center space-x-2">
                        <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>LOCKING IN...</span>
                    </span>
                    <span x-show="isReady" style="display:none;" class="flex items-center space-x-2">
                        <span>✓ READY TO BLITZ!</span>
                    </span>
                </button>
            </div>

            <div class="pt-2">
                <p class="text-lg font-black uppercase tracking-widest leading-none drop-shadow-lg"
                   :class="isReady ? 'text-green-400' : 'text-white'"
                   x-text="isReady ? 'READY TO BATTLE!' : 'YOU\'RE IN THE LOBBY!'"></p>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-[11px] mt-2" 
                   x-text="isReady ? 'Waiting for host to start the game.' : 'Click I\'M READY above to lock in your readiness!'"></p>
            </div>
        </div>
    </div>

    <!-- Background Decoration Circles -->
    <div class="fixed -bottom-32 -left-32 w-96 h-96 bg-purple-600/5 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="fixed -top-32 -right-32 w-96 h-96 bg-pink-600/5 rounded-full blur-[120px] pointer-events-none"></div>
</div>
@endsection
