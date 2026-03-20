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
         }
     }" 
     x-init="checkStatus(); setInterval(() => checkStatus(), 3000)">
    
    <!-- Top BrainBlitz Logo -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-black italic tracking-tighter uppercase"><span class="text-gradient">BRAIN</span>BLITZ</h1>
    </div>

    <!-- Centered Waiting Content -->
    <div class="card p-16 rounded-[4rem] w-full max-w-2xl text-center space-y-10 shadow-[0_0_80px_rgba(168,85,247,0.1)] border-white/10 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-pink-600/10 opacity-50"></div>
        
        <div class="relative space-y-8 animate-in fade-in zoom-in duration-700">
            <!-- Pulsing Loading Indicator -->
            <div class="w-32 h-32 bg-purple-600/20 rounded-full mx-auto flex items-center justify-center border border-white/20 animate-pulse">
                <div class="w-24 h-24 bg-purple-500/40 rounded-full animate-ping opacity-50"></div>
                <div class="absolute w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(168,85,247,0.8)]">
                     <span class="text-white text-3xl font-black uppercase tracking-tighter italic">⚡</span>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-6xl font-black uppercase text-gradient italic tracking-tighter animate-pulse grow">WAITING FOR HOST...</h2>
                <h3 class="text-3xl font-black uppercase tracking-tighter text-white">{{ $room->quiz->title }}</h3>
                <p class="text-gray-500 font-black uppercase tracking-widest text-sm italic">Arena Code: <span class="text-purple-400 font-mono">{{ $room->room_code }}</span></p>
            </div>

            <div class="flex flex-col space-y-4">
                <div class="bg-white/5 py-3 px-8 border border-white/10 rounded-2xl flex items-center justify-between">
                    <span class="text-gray-400 font-black uppercase tracking-widest text-[9px]">Squad Size</span>
                    <span class="text-white font-black italic text-sm" x-text="count + ' / ' + max"></span>
                </div>
                
                <div class="py-3 px-8 border border-white/10 rounded-2xl flex items-center justify-between transition-all"
                     :class="readyCount === totalCount && totalCount > 0 ? 'bg-green-500/10 border-green-500/30' : 'bg-white/5 border-white/10'">
                    <span class="text-gray-400 font-black uppercase tracking-widest text-[9px]">Ready Count</span>
                    <span class="font-black italic text-sm" 
                          :class="readyCount === totalCount && totalCount > 0 ? 'text-green-500' : 'text-gray-600'"
                          x-text="readyCount + ' / ' + totalCount"></span>
                </div>
            </div>

            <div class="pt-8">
                <p class="text-2xl font-black uppercase tracking-widest leading-none drop-shadow-lg"
                   :class="readyCount === totalCount && totalCount > 0 ? 'text-green-500' : 'text-white'"
                   x-text="readyCount === totalCount && totalCount > 0 ? 'ALL READY!' : 'YOU\'RE IN!'"></p>
                <p class="text-gray-500 font-bold uppercase tracking-widest text-xs mt-4" x-text="readyCount === totalCount && totalCount > 0 ? 'Prepare for immediate launch.' : 'Get ready to compete. The battle is about to begin.'"></p>
            </div>
        </div>
    </div>

    <!-- Background Decoration Circles -->
    <div class="fixed -bottom-32 -left-32 w-96 h-96 bg-purple-600/5 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="fixed -top-32 -right-32 w-96 h-96 bg-pink-600/5 rounded-full blur-[120px] pointer-events-none"></div>
</div>
@endsection
