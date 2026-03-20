@extends('layouts.app')

@section('title', 'Edit Topic: ' . $topic->name)

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col items-center p-6 space-y-12 pb-24">
    <div class="w-full max-w-4xl flex items-center justify-between mb-10 text-left">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-none grow"><span class="text-gradient">EDIT</span><br> TOPIC</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4">Modify category details for <span class="text-white">{{ $topic->name }}</span>.</p>
        </div>
        <a href="{{ route('admin.topics.index') }}" class="font-bold tracking-widest text-sm uppercase text-gray-500 hover:text-white transition-colors">Cancel Changes</a>
    </div>

    <div class="card p-12 rounded-3xl w-full max-w-4xl shadow-2xl relative overflow-hidden group">
        <!-- Floating Circles for energy -->
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-purple-600/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-pink-600/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>

        <form action="{{ route('admin.topics.update', $topic->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-widest">Topic Name</label>
                <input type="text" name="name" value="{{ old('name', $topic->name) }}" 
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/20 text-lg font-bold text-white uppercase tracking-tighter" required>
                @error('name')
                    <p class="text-pink-500 text-sm mt-2 font-semibold tracking-widest uppercase text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-widest">Description</label>
                <textarea name="description" rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/20 font-bold">{{ old('description', $topic->description) }}</textarea>
                @error('description')
                    <p class="text-pink-500 text-sm mt-2 font-semibold tracking-widest uppercase text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full btn-primary px-8 py-5 rounded-2xl font-black text-xl tracking-tighter uppercase shadow-2xl">
                    Update Topic Archive
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
