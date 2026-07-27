@extends('layouts.app')

@section('title', 'Tune Arena: ' . $quiz->title)

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col items-center p-6 space-y-10 pb-24" x-data="{ topicMode: '{{ old('topic_mode', $quiz->topic_mode) }}', timeValue: {{ old('time_per_question', $quiz->time_per_question) }}, participants: {{ old('max_participants', $quiz->max_participants) }} }">
    <div class="w-full max-w-4xl flex items-center justify-between mb-4">
        <div>
            <h1 class="text-5xl font-black italic tracking-tighter uppercase leading-none grow"><span class="text-gradient">TUNE</span> ARENA</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-2 text-xs">Adjust engine settings for <span class="text-white">{{ $quiz->title }}</span>.</p>
        </div>
        <a href="{{ route('admin.quizzes.show', $quiz->id) }}" class="font-bold tracking-widest text-xs uppercase text-gray-500 hover:text-white transition-colors flex items-center gap-2">
            <span>✕</span> Discard Adjustments
        </a>
    </div>

    <div class="card p-8 md:p-12 rounded-3xl w-full max-w-4xl shadow-2xl relative overflow-hidden">
        <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST" class="space-y-12">
            @csrf
            @method('PUT')
            
            <!-- Hidden input fallback for topic_mode -->
            <input type="hidden" name="topic_mode" :value="topicMode" value="{{ old('topic_mode', $quiz->topic_mode) }}">

            <!-- STEP 1: Arena Title -->
            <div class="space-y-4 border-b border-white/10 pb-8">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-purple-500 text-white rounded-lg text-xs font-black uppercase tracking-widest">STEP 1</span>
                    <h2 class="text-xl font-black uppercase italic tracking-wider text-white">Arena Title</h2>
                </div>
                <div>
                    <input type="text" name="title" value="{{ old('title', $quiz->title) }}" 
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 focus:ring-2 focus:ring-purple-500 outline-none transition-all text-2xl font-black italic tracking-tighter uppercase text-white" 
                        required>
                    @error('title')
                        <p class="text-pink-500 text-xs mt-2 font-semibold tracking-widest uppercase">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- STEP 2: Knowledge Engine Mode -->
            <div class="space-y-6 border-b border-white/10 pb-8">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-purple-500 text-white rounded-lg text-xs font-black uppercase tracking-widest">STEP 2</span>
                    <h2 class="text-xl font-black uppercase italic tracking-wider text-white">Knowledge Engine Mode</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label @click="topicMode = 'single'" 
                         :class="topicMode == 'single' ? 'border-purple-500 bg-purple-500/15 ring-2 ring-purple-500/50 shadow-lg shadow-purple-500/20' : 'border-white/10 bg-white/5 opacity-70 hover:opacity-100'"
                         class="cursor-pointer border-2 p-6 rounded-2xl transition-all flex flex-col items-center text-center relative select-none">
                         <div x-show="topicMode == 'single'" class="absolute top-3 right-3 px-2 py-0.5 bg-purple-500 text-white rounded-md text-[9px] font-black uppercase tracking-widest">✓ ACTIVE</div>
                         <input type="radio" name="_mode_radio" value="single" x-model="topicMode" class="sr-only">
                         <span class="text-4xl mb-3">🎯</span>
                         <h3 class="text-lg font-black uppercase tracking-tighter italic" :class="topicMode == 'single' ? 'text-white' : 'text-gray-300'">Single Topic</h3>
                         <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mt-2">1 category focus</p>
                    </label>

                    <label @click="topicMode = 'multiple'" 
                         :class="topicMode == 'multiple' ? 'border-pink-500 bg-pink-500/15 ring-2 ring-pink-500/50 shadow-lg shadow-pink-500/20' : 'border-white/10 bg-white/5 opacity-70 hover:opacity-100'"
                         class="cursor-pointer border-2 p-6 rounded-2xl transition-all flex flex-col items-center text-center relative select-none">
                         <div x-show="topicMode == 'multiple'" class="absolute top-3 right-3 px-2 py-0.5 bg-pink-500 text-white rounded-md text-[9px] font-black uppercase tracking-widest">✓ ACTIVE</div>
                         <input type="radio" name="_mode_radio" value="multiple" x-model="topicMode" class="sr-only">
                         <span class="text-4xl mb-3">🌀</span>
                         <h3 class="text-lg font-black uppercase tracking-tighter italic" :class="topicMode == 'multiple' ? 'text-white' : 'text-gray-300'">Multiple Topics</h3>
                         <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mt-2">Combine topics</p>
                    </label>

                    <label @click="topicMode = 'random'" 
                         :class="topicMode == 'random' ? 'border-blue-500 bg-blue-500/15 ring-2 ring-blue-500/50 shadow-lg shadow-blue-500/20' : 'border-white/10 bg-white/5 opacity-70 hover:opacity-100'"
                         class="cursor-pointer border-2 p-6 rounded-2xl transition-all flex flex-col items-center text-center relative select-none">
                         <div x-show="topicMode == 'random'" class="absolute top-3 right-3 px-2 py-0.5 bg-blue-500 text-white rounded-md text-[9px] font-black uppercase tracking-widest">✓ ACTIVE</div>
                         <input type="radio" name="_mode_radio" value="random" x-model="topicMode" class="sr-only">
                         <span class="text-4xl mb-3">🎲</span>
                         <h3 class="text-lg font-black uppercase tracking-tighter italic" :class="topicMode == 'random' ? 'text-white' : 'text-gray-300'">Randomized</h3>
                         <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mt-2">All topics</p>
                    </label>
                </div>
                @error('topic_mode')
                    <p class="text-pink-500 text-xs mt-2 font-semibold tracking-widest uppercase text-center">{{ $message }}</p>
                @enderror
            </div>

            <!-- STEP 3: Topic Selection -->
            <div class="space-y-6 border-b border-white/10 pb-8">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-purple-500 text-white rounded-lg text-xs font-black uppercase tracking-widest">STEP 3</span>
                    <h2 class="text-xl font-black uppercase italic tracking-wider text-white">Target Topics</h2>
                </div>

                <div x-show="topicMode == 'single'" class="p-6 bg-purple-500/10 border border-purple-500/30 rounded-2xl space-y-3">
                    <label class="block text-xs font-bold text-purple-300 uppercase tracking-widest">Select Prime Focus Topic</label>
                    <select name="topic_ids[]" class="w-full bg-black/60 border border-white/20 rounded-xl px-4 py-3 text-white font-bold transition-all focus:ring-2 focus:ring-purple-500 outline-none">
                        <option value="">-- Choose a topic --</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" {{ in_array($topic->id, old('topic_ids', $quiz->topics->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $topic->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="topicMode == 'multiple'" class="p-6 bg-pink-500/10 border border-pink-500/30 rounded-2xl space-y-4">
                    <label class="block text-xs font-bold text-pink-300 uppercase tracking-widest">Select Target Topics</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($topics as $topic)
                            <label class="flex items-center space-x-3 p-3 bg-black/40 rounded-xl cursor-pointer border border-white/10 hover:border-pink-500/50 transition-colors">
                                <input type="checkbox" name="topic_ids[]" value="{{ $topic->id }}" 
                                    {{ in_array($topic->id, old('topic_ids', $quiz->topics->pluck('id')->toArray())) ? 'checked' : '' }}
                                    class="w-5 h-5 accent-pink-500 rounded">
                                <span class="text-xs font-bold uppercase text-gray-200 truncate">{{ $topic->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="topicMode == 'random'" class="p-6 bg-blue-500/10 border border-blue-500/30 rounded-2xl text-center space-y-2">
                     <p class="text-lg font-black italic uppercase tracking-tighter text-blue-400">Pure Procedural Mode</p>
                     <p class="text-gray-300 font-bold uppercase tracking-wider text-xs">Questions from ALL available topics will be injected into this arena.</p>
                </div>

                @error('topic_ids')
                    <p class="text-pink-500 text-xs mt-2 font-semibold tracking-widest uppercase">{{ $message }}</p>
                @enderror
            </div>

            <!-- STEP 4: Parameters -->
            <div class="space-y-6 border-b border-white/10 pb-8">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-purple-500 text-white rounded-lg text-xs font-black uppercase tracking-widest">STEP 4</span>
                    <h2 class="text-xl font-black uppercase italic tracking-wider text-white">Parameters</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4 bg-white/5 p-6 rounded-2xl border border-white/10">
                        <div class="flex items-center justify-between">
                             <label class="text-xs font-bold text-gray-300 uppercase tracking-widest">Time Limit Per Question</label>
                             <span class="text-2xl font-black italic tracking-tighter text-purple-400" x-text="timeValue + 's'"></span>
                        </div>
                        <input type="range" name="time_per_question" min="10" max="120" step="5" x-model="timeValue" 
                            class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-purple-500">
                    </div>

                    <div class="space-y-4 bg-white/5 p-6 rounded-2xl border border-white/10">
                        <div class="flex items-center justify-between">
                             <label class="text-xs font-bold text-gray-300 uppercase tracking-widest">Max Participants</label>
                             <span class="text-2xl font-black italic tracking-tighter text-pink-400" x-text="participants + ' players'"></span>
                        </div>
                        <input type="range" name="max_participants" min="2" max="100" x-model="participants" 
                            class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-pink-500">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full btn-primary px-8 py-5 rounded-2xl font-black text-2xl tracking-tighter uppercase shadow-2xl hover:scale-[1.01] transition-transform">
                    Finalize Adjustments
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
