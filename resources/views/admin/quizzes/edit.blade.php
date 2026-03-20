@extends('layouts.app')

@section('title', 'Tune Arena: ' . $quiz->title)

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col items-center p-6 space-y-12 pb-24" x-data="{ topicMode: '{{ $quiz->topic_mode }}', timeValue: {{ $quiz->time_per_question }}, participants: {{ $quiz->max_participants }} }">
    <div class="w-full max-w-4xl flex items-center justify-between mb-10">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-none grow"><span class="text-gradient">TUNE</span><br> ARENA</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4">Adjust engine settings for <span class="text-white">{{ $quiz->title }}</span>.</p>
        </div>
        <a href="{{ route('admin.quizzes.show', $quiz->id) }}" class="font-bold tracking-widest text-sm uppercase text-gray-500 hover:text-white transition-colors">Discard Adjustments</a>
    </div>

    <div class="card p-12 rounded-3xl w-full max-w-4xl shadow-2xl relative overflow-hidden group">
        <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST" class="space-y-12">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-widest text-left">Arena Title</label>
                    <input type="text" name="title" value="{{ old('title', $quiz->title) }}" 
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-6 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/20 text-3xl font-black italic tracking-tighter uppercase leading-tight" 
                        required>
                    @error('title')
                        <p class="text-pink-500 text-sm mt-2 font-semibold tracking-widest uppercase text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Topic Mode -->
            <div>
                <label class="block text-sm font-bold text-gray-400 mb-6 uppercase tracking-widest text-left">Knowledge Engine Mode</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <input type="hidden" name="topic_mode" :value="topicMode">
                    
                    <div @click="topicMode = 'single'" 
                         :class="topicMode == 'single' ? 'border-purple-500 bg-purple-500/10 shadow-lg shadow-purple-500/20' : 'border-white/10 bg-white/5 opacity-50'"
                         class="cursor-pointer border-2 p-6 rounded-2xl transition-all group flex flex-col items-center text-center">
                         <span class="text-4xl mb-4">🎯</span>
                         <h3 class="text-lg font-black uppercase tracking-tighter italic" :class="topicMode == 'single' ? 'text-white' : 'text-gray-400'">Single Topic</h3>
                    </div>

                    <div @click="topicMode = 'multiple'" 
                         :class="topicMode == 'multiple' ? 'border-pink-500 bg-pink-500/10 shadow-lg shadow-pink-500/20' : 'border-white/10 bg-white/5 opacity-50'"
                         class="cursor-pointer border-2 p-6 rounded-2xl transition-all group flex flex-col items-center text-center">
                         <span class="text-4xl mb-4">🌀</span>
                         <h3 class="text-lg font-black uppercase tracking-tighter italic" :class="topicMode == 'multiple' ? 'text-white' : 'text-gray-400'">Multiple Topics</h3>
                    </div>

                    <div @click="topicMode = 'random'" 
                         :class="topicMode == 'random' ? 'border-blue-500 bg-blue-500/10 shadow-lg shadow-blue-500/20' : 'border-white/10 bg-white/5 opacity-50'"
                         class="cursor-pointer border-2 p-6 rounded-2xl transition-all group flex flex-col items-center text-center">
                         <span class="text-4xl mb-4">🎲</span>
                         <h3 class="text-lg font-black uppercase tracking-tighter italic" :class="topicMode == 'random' ? 'text-white' : 'text-gray-400'">Randomized</h3>
                    </div>
                </div>
                @error('topic_mode')
                    <p class="text-pink-500 text-sm mt-4 font-semibold tracking-widest uppercase text-xs text-center">{{ $message }}</p>
                @enderror
            </div>

            <!-- Topic Selection -->
            <div x-show="topicMode != 'random'" x-transition class="p-8 bg-white/5 border border-white/10 rounded-2xl shadow-inner shadow-black/20 text-left">
                <template x-if="topicMode == 'single'">
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-4 uppercase tracking-widest">Select Prime Focus Topic</label>
                        <select name="topic_ids[]" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 font-bold transition-all focus:ring-2 focus:ring-purple-500 outline-none">
                            <option value="">Choose a topic...</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ in_array($topic->id, $quiz->topics->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                <template x-if="topicMode == 'multiple'">
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-6 uppercase tracking-widest">Select Target Archive Topics</label>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($topics as $topic)
                                <label class="flex items-center space-x-3 p-3 bg-black/20 rounded-lg cursor-pointer hover:bg-black/40 transition-colors">
                                    <input type="checkbox" name="topic_ids[]" value="{{ $topic->id }}" 
                                        {{ in_array($topic->id, $quiz->topics->pluck('id')->toArray()) ? 'checked' : '' }}
                                        class="w-5 h-5 accent-pink-500">
                                    <span class="text-xs font-black uppercase text-gray-400 truncate">{{ $topic->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </template>
                @error('topic_ids')
                    <p class="text-pink-500 text-sm mt-4 font-semibold tracking-widest uppercase text-xs">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sliders -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-6 text-left">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                         <label class="text-sm font-bold text-gray-400 uppercase tracking-widest">Time Response Limit</label>
                         <span class="text-2xl font-black italic tracking-tighter text-gradient grow text-right" x-text="timeValue + 's'"></span>
                    </div>
                    <input type="range" name="time_per_question" min="10" max="120" step="5" x-model="timeValue" 
                        class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-purple-500">
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                         <label class="text-sm font-bold text-gray-400 uppercase tracking-widest">Max Participants</label>
                         <span class="text-2xl font-black italic tracking-tighter text-white grow text-right" x-text="participants"></span>
                    </div>
                    <input type="range" name="max_participants" min="2" max="100" x-model="participants" 
                        class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-pink-500">
                </div>
            </div>

            <div class="pt-10">
                <button type="submit" class="w-full btn-primary px-8 py-6 rounded-2xl font-black text-2xl tracking-tighter uppercase shadow-2xl">
                    Finalize Adjustments
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
