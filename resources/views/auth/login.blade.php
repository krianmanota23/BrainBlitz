@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex-1 flex items-center justify-center p-6">
    <div class="card p-10 rounded-3xl w-full max-w-md shadow-2xl relative overflow-hidden group">
        <!-- Floating Circles for energy -->
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-purple-600/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-pink-600/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>

        <div class="text-center mb-10">
            <h1 class="text-6xl font-black mb-2 tracking-tighter italic">
                <span class="text-gradient">BRAIN</span><br>
                <span class="text-white">BLITZ</span>
            </h1>
            <p class="text-gray-400 font-medium">Assumption College of Davao</p>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-200 p-4 rounded-xl mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-xl mb-6 text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-widest">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/20" 
                    placeholder="Enter your username" required>
                @error('username')
                    <p class="text-pink-500 text-sm mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-widest">Password</label>
                <input type="password" name="password" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/20" 
                    placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full btn-primary px-8 py-5 rounded-2xl font-black text-xl tracking-tighter uppercase">
                Ready to Blitz?
            </button>
        </form>

        <p class="mt-10 text-center text-gray-500 font-bold">
            New here? <a href="{{ url('/register') }}" class="text-purple-400 hover:text-pink-400 transition-colors">Create an account</a>
        </p>
    </div>
</div>
@endsection
