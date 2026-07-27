@extends('layouts.app')

@section('title', 'Enter The Arena')

@section('content')
@include('student.partials.navbar')

<div class="flex-1 flex flex-col items-center justify-center p-6 bg-[#0f0f1a] relative overflow-hidden min-h-screen" 
     x-data="{ code: '', loading: false }">
    
    <!-- Cinematic Layers -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-20">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-600 rounded-full blur-[200px] animate-pulse"></div>
    </div>

    <div class="card p-12 rounded-[3.5rem] w-full max-w-lg shadow-2xl relative z-10 border-white/10 group">
        <!-- Logo -->
        <div class="text-center mb-14">
            <h1 class="text-5xl font-black mb-10 tracking-tighter italic uppercase leading-none select-none">
                <span class="text-gradient">BRAIN</span>BLITZ
            </h1>
            <div class="space-y-4 text-center">
                <h2 class="text-3xl font-black uppercase text-white tracking-[0.2em] italic">JOIN <span class="text-gradient">GAME</span></h2>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] italic">Enter the room code from your teacher</p>
            </div>
        </div>

        {{-- Session Error --}}
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-8 text-center font-bold text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-8 text-center font-bold text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('student.join.room') }}" @submit="loading = true" class="space-y-10">
            @csrf
            <div class="relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-[#1a1a2e] border border-white/10 rounded-full z-20 text-[9px] font-black uppercase text-gray-400 tracking-widest italic select-none">
                    ROOM CODE
                </div>
                <input type="text" name="room_code" x-model="code" maxlength="12"
                    x-on:paste="setTimeout(() => { let v = $event.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().slice(0, 6); $event.target.value = v; code = v; }, 10)"
                    x-on:input="let v = $event.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().slice(0, 6); $event.target.value = v; code = v;"
                    class="w-full bg-white/5 border-2 border-white/10 rounded-[2.5rem] px-8 py-10 focus:ring-4 focus:ring-purple-500/30 focus:border-purple-500 text-center text-6xl font-mono font-black tracking-[0.4em] uppercase placeholder-white/10 outline-none transition-all shadow-inner text-white" 
                    placeholder="______" required autocomplete="off" spellcheck="false" inputmode="text" 
                    autofocus>
            </div>

            <button type="submit" :disabled="loading || code.length < 6"
                class="w-full px-10 py-8 rounded-2xl font-black text-2xl tracking-tighter uppercase flex items-center justify-center text-white shadow-[0_0_50px_rgba(168,85,247,0.3)] disabled:opacity-50 bg-green-600 hover:bg-green-500 transition-all min-h-[5rem]">
                <span x-show="!loading">Join Arena &rarr;</span>
                <span x-show="loading" style="display: none;" class="flex items-center space-x-3">
                    <svg class="animate-spin text-white" width="28" height="28" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Joining...</span>
                </span>
            </button>
        </form>

        <div class="mt-14 text-center">
            <div class="bg-white/5 inline-flex items-center px-8 py-4 rounded-3xl border border-white/10">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse mr-4"></div>
                <span class="text-gray-500 font-black uppercase tracking-widest text-[9px] mr-4">Playing as:</span>
                <span class="text-white font-black italic uppercase tracking-tighter text-xs">{{ $nickname }}</span>
            </div>
        </div>

        <p class="mt-12 text-center text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] italic">
            <a href="{{ route('student.dashboard') }}" class="hover:text-purple-400 transition-colors">&larr; Return to Dashboard</a>
        </p>
    </div>
</div>
@endsection
