@extends('layouts.app')

@section('title', '403 - ACCESS DENIED')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center p-8 space-y-12 h-screen overflow-hidden relative">
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none opacity-20">
        <div class="absolute -top-24 -left-24 w-[500px] h-[500px] bg-red-800 rounded-full blur-[200px] animate-pulse"></div>
    </div>

    <div class="text-center space-y-8 z-10">
        <h1 class="text-[15rem] font-black italic tracking-tighter uppercase text-gradient leading-none opacity-20 select-none">403</h1>
        <div class="space-y-4 absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-48">
            <h2 class="text-5xl font-black uppercase tracking-tighter text-white italic">ACCESS <span class="text-red-500">DENIED</span></h2>
            <p class="text-gray-500 font-bold uppercase tracking-widest text-sm max-w-lg mx-auto leading-relaxed">You don't have the clearance for this level of the arena. Only authorized battle lords can pass this point.</p>
        </div>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-center gap-6 z-10 pt-48 mt-12">
        <a href="{{ url()->previous() }}" class="px-10 py-5 rounded-2xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-xs transition-all shadow-xl">Fall Back</a>
        <a href="{{ url('/') }}" class="btn-primary px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-xs text-white shadow-xl">Return to Dash</a>
    </div>
</div>
@endsection
