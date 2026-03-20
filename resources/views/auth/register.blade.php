@extends('layouts.app')

@section('title', 'Join BrainBlitz')

@section('content')
<div class="flex-1 flex items-center justify-center p-6 min-h-screen bg-[#0f0f1a] relative overflow-hidden">
    <!-- Animated background layers -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-30">
        <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-purple-600 rounded-full blur-[200px] animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[600px] h-[600px] bg-pink-600 rounded-full blur-[200px] animate-pulse delay-1000"></div>
    </div>

    <div class="card p-12 rounded-[3rem] w-full max-w-xl shadow-2xl relative z-10 border-white/5 group" 
         x-data="{ 
            username: '{{ old('username') }}', 
            available: null, 
            checking: false,
            loading: false,
            async checkUsername() {
                if(this.username.length < 3) { this.available = null; return; }
                this.checking = true;
                try {
                    const response = await fetch(`/check-username?username=${this.username}`);
                    const data = await response.json();
                    this.available = data.available;
                } catch(e) { console.error(e); }
                this.checking = false;
            }
         }">
        
        <div class="text-center mb-12">
            <h1 class="text-7xl font-black mb-2 tracking-tighter italic uppercase leading-tight select-none">
                <span class="text-gradient">BRAIN</span><br>
                <span class="text-white">BLITZ</span>
            </h1>
            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-500 italic">Initiate Combat Protocols</p>
        </div>

        <form @submit="loading = true" action="{{ url('/register') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-500 mb-3 uppercase tracking-widest italic">Full Identity <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" 
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/10 text-white font-bold" 
                    placeholder="Enter your full name" required>
                @error('full_name')<p class="text-red-500 font-black uppercase tracking-widest text-[9px] mt-2 italic">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2 relative">
                    <label class="block text-[10px] font-black text-gray-500 mb-3 uppercase tracking-widest italic">Target Username <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="username" x-model="username" @input.debounce.500ms="checkUsername()"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/10 text-white font-bold" 
                            placeholder="codenamer_x" required>
                        
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 flex items-center">
                            <template x-if="checking">
                                <svg class="animate-spin h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <template x-if="!checking && available === true">
                                <span class="text-green-500 font-black text-xs">READY ✓</span>
                            </template>
                            <template x-if="!checking && available === false">
                                <span class="text-red-500 font-black text-xs">TAKEN 🚨</span>
                            </template>
                        </div>
                    </div>
                    @error('username')<p class="text-red-500 font-black uppercase tracking-widest text-[9px] mt-2 italic">{{ $message }}</p>@enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 mb-3 uppercase tracking-widest italic leading-none">Nickname</label>
                    <input type="text" name="nickname" value="{{ old('nickname') }}" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/10 text-white font-bold" 
                        placeholder="Battle Tag (Optional)">
                    @error('nickname')<p class="text-red-500 font-black uppercase tracking-widest text-[9px] mt-2 italic">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-500 mb-3 uppercase tracking-widest italic">Secret Key <span class="text-red-500">*</span></label>
                    <input type="password" name="password" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/10 text-white font-bold" 
                        placeholder="••••••••" required>
                    @error('password')<p class="text-red-500 font-black uppercase tracking-widest text-[9px] mt-2 italic">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-500 mb-3 uppercase tracking-widest italic">Verify Key <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/10 text-white font-bold" 
                        placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" :disabled="loading || available === false" class="w-full btn-primary px-8 py-6 rounded-3xl font-black text-2xl tracking-tighter uppercase shadow-2xl flex items-center justify-center space-x-4 transition-transform active:scale-95">
                <span x-show="!loading">INITIALIZE BLITZ</span>
                <span x-show="loading" class="flex items-center space-x-3">
                    <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>JOINING...</span>
                </span>
            </button>
        </form>

        <p class="mt-12 text-center text-gray-500 font-black uppercase tracking-[0.2em] text-[10px]">
            Already authorized? <a href="{{ url('/login') }}" class="text-purple-400 hover:text-pink-400 transition-colors italic underline">Sync Account &rarr;</a>
        </p>
    </div>
</div>
@endsection
