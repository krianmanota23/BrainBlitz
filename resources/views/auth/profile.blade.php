@extends('layouts.app')

@section('title', 'Warrior Profile Sync')

@section('content')
@if(Auth::user()->role === 'admin')
    @include('admin.partials.navbar')
@else
    @include('student.partials.navbar')
@endif

<div class="flex-1 flex flex-col p-8 space-y-12 pb-24 max-w-4xl mx-auto w-full">
    <!-- Profile Header -->
    <div class="flex flex-col md:flex-row items-center gap-10">
        <div class="relative group">
            <div class="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-5xl font-black italic text-white shadow-2xl transform rotate-3 group-hover:rotate-0 transition-transform">
                {{ substr($user->nickname, 0, 1) }}
            </div>
            <div class="absolute -bottom-2 -right-2 px-3 py-1 bg-white rounded-lg text-[9px] font-black uppercase text-purple-600 shadow-xl border border-purple-100">
                {{ $user->role }}
            </div>
        </div>
        
        <div class="text-center md:text-left">
            <h1 class="text-4xl font-black uppercase tracking-tighter italic text-white">{{ $user->full_name }}</h1>
            <p class="text-gray-500 font-bold tracking-widest uppercase text-sm">@ {{ $user->username }}</p>
            <div class="mt-4 flex items-center justify-center md:justify-start space-x-3">
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-black uppercase tracking-widest text-[#94a3b8]">Verified Soldier</span>
            </div>
        </div>
    </div>

    @if($stats)
    <!-- Stats Grid (Students Only) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statIcons = [
                ['label' => 'Total Blitz', 'val' => $stats['games_played'], 'icon' => '🎮'],
                ['label' => 'Record Rank', 'val' => '#'.$stats['best_rank'], 'icon' => '🏆'],
                ['label' => 'Performance', 'val' => number_format($stats['total_score']), 'icon' => '⚡'],
                ['label' => 'Hit Accuracy', 'val' => $stats['accuracy'].'%', 'icon' => '🎯'],
            ];
        @endphp
        @foreach($statIcons as $s)
        <div class="card p-6 rounded-2xl text-center">
             <span class="text-2xl block mb-2">{{ $s['icon'] }}</span>
             <p class="text-xl font-black italic text-white tabular-nums">{{ $s['val'] }}</p>
             <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 gap-8">
        <!-- Nickname Form -->
        <div class="card p-8 rounded-3xl" x-data="{ editing: false, nickname: '{{ $user->nickname }}' }">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-gray-500 font-black uppercase tracking-widest text-xs">Identity Sync</h3>
                <button @click="editing = !editing" class="text-xs font-black uppercase tracking-widest text-purple-400 hover:text-white transition-colors">
                    <span x-show="!editing">Edit Nickname</span>
                    <span x-show="editing">Cancel Sync</span>
                </button>
            </div>

            <div x-show="!editing" class="flex items-center space-x-4">
                 <p class="text-2xl font-black italic uppercase tracking-tighter text-white">{{ $user->nickname }}</p>
                 <span class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">(Current Nickname)</span>
            </div>

            <form x-show="editing" action="{{ route('profile.nickname') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Target Nickname *</label>
                    <input type="text" name="nickname" x-model="nickname" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-white focus:outline-none focus:border-purple-500 transition-all font-bold">
                </div>
                <button type="submit" class="btn-primary w-full py-4 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-xl">Apply Identity Update</button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="card p-8 rounded-3xl" x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between group">
                <h3 class="text-gray-500 font-black uppercase tracking-widest text-xs group-hover:text-white transition-colors">Security Protocol Updates</h3>
                <span class="text-2xl transform transition-transform" :class="open ? 'rotate-180' : ''">⌄</span>
            </button>

            <form x-show="open" action="{{ route('profile.password') }}" method="POST" class="mt-8 space-y-6" x-data="{ newPass: '' }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Current Password *</label>
                        <input type="password" name="current_password" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-white focus:outline-none focus:border-purple-500 transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">New Cipher Key *</label>
                        <input type="password" name="password" required x-model="newPass"
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-white focus:outline-none focus:border-purple-500 transition-all">
                        
                        <!-- Strength Indicator -->
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden flex gap-1">
                             <div class="h-full bg-red-500 transition-all duration-500" :style="newPass.length > 0 ? 'width: 33.3%' : 'width: 0%'"></div>
                             <div class="h-full bg-yellow-500 transition-all duration-500" :style="newPass.length > 8 ? 'width: 33.3%' : 'width: 0%'"></div>
                             <div class="h-full bg-green-500 transition-all duration-500" :style="newPass.length > 12 && /[A-Z]/.test(newPass) ? 'width: 33.4%' : 'width: 0%'"></div>
                        </div>
                        <p class="text-[9px] font-bold uppercase tracking-widest"
                           :class="newPass.length < 8 ? 'text-red-500' : (newPass.length < 13 ? 'text-yellow-500' : 'text-green-500')">
                             <span x-show="newPass.length < 8">Vulnerable</span>
                             <span x-show="newPass.length >= 8 && newPass.length < 13">Moderate</span>
                             <span x-show="newPass.length >= 13">Fortified</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Confirm Cipher Key *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-white focus:outline-none focus:border-purple-500 transition-all">
                    </div>
                </div>
                <button type="submit" class="btn-primary px-10 py-4 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-xl">Commit Security Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
