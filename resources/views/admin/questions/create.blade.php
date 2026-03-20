@extends('layouts.app')

@section('title', 'Inject New Question')

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col items-center p-6 space-y-12 pb-24 max-w-4xl mx-auto w-full" 
     x-data="{ timeLimit: {{ $quiz->time_per_question }}, correctIndex: 0, questionText: '', loading: false }">
    
    <div class="w-full flex items-center justify-between mb-10">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-none grow"><span class="text-gradient">NEW</span><br> CHALLENGE</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4 text-left grow italic text-[10px]">Designing sequence #{{ $quiz->questions_count + 1 }} for <span class="text-white">{{ $quiz->title }}</span>.</p>
        </div>
        <a href="{{ route('admin.quizzes.show', $quiz->id) }}" class="font-black tracking-widest text-[10px] uppercase text-gray-500 hover:text-white transition-colors">Discard Challenge</a>
    </div>

    <div class="card p-12 rounded-[2.5rem] w-full shadow-2xl relative overflow-hidden group">
        <form @submit="loading = true" action="{{ route('admin.quizzes.questions.store', $quiz->id) }}" method="POST" class="space-y-12 text-left">
            @csrf
            
            <!-- Question Text -->
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest italic leading-none">Question Transcript <span class="text-red-500">*</span></label>
                        <span class="text-[9px] font-black uppercase tracking-widest" :class="questionText.length > 450 ? 'text-red-500' : 'text-gray-600'">
                            <span x-text="questionText.length"></span> / 500 characters
                        </span>
                    </div>
                    <textarea name="question_text" rows="3" x-model="questionText" maxlength="500"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-6 focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-white/10 text-2xl font-black tracking-tighter text-white" 
                        placeholder="Type your strategic challenge here..." required></textarea>
                    @error('question_text')
                        <p class="text-red-500 font-black uppercase tracking-widest text-[9px] mt-3 italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Topic and Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 mb-4 uppercase tracking-widest italic">Target Knowledge Domain <span class="text-red-500">*</span></label>
                    <select name="topic_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-6 py-4 focus:ring-2 focus:ring-purple-500 outline-none transition-all font-black text-xs text-white appearance-none cursor-pointer">
                        <option value="" class="bg-[#0f0f1a]">Select domain...</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" class="bg-[#0f0f1a]">{{ $topic->name }}</option>
                        @endforeach
                    </select>
                    @error('topic_id')
                        <p class="text-red-500 font-black uppercase tracking-widest text-[9px] mt-2 italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                         <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Reaction Buffer</label>
                         <span class="text-xl font-black italic tracking-tighter text-gradient" x-text="timeLimit + 's'"></span>
                    </div>
                    <input type="range" name="time_limit" min="10" max="120" step="5" x-model="timeLimit" 
                        class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-purple-500">
                </div>
            </div>

            <!-- Options Design -->
            <div class="space-y-8">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Input Buffers & Master Solution <span class="text-red-500">*</span></label>
                
                <div class="grid grid-cols-1 gap-6">
                    @foreach(['red', 'blue', 'yellow', 'green'] as $index => $color)
                    <div class="flex items-center space-x-6 group/opt">
                        <div class="flex-1 relative">
                            <input type="text" name="options[{{ $index }}][text]" 
                                class="w-full bg-white/5 border-2 border-white/5 rounded-2xl px-12 py-5 focus:border-{{ $color }}-500 focus:bg-{{ $color }}-500/5 outline-none transition-all font-black text-white placeholder-white/10" 
                                placeholder="{{ ucfirst($color) }} Buffer..." required>
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-{{ $color }}-500 shadow-[0_0_10px_rgba(var(--{{ $color }}-rgb),0.5)]"></div>
                            <input type="hidden" name="options[{{ $index }}][color]" value="{{ $color }}">
                        </div>
                        <label class="cursor-pointer group/radio">
                            <input type="radio" name="correct_option" value="{{ $index }}" x-model="correctIndex" class="hidden">
                            <div :class="correctIndex == {{ $index }} ? 'bg-green-500 scale-110 shadow-[0_0_20px_rgba(34,197,94,0.4)]' : 'bg-white/5 opacity-20 group-hover/radio:opacity-100' " 
                                 class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all border border-white/10">
                                <svg x-show="correctIndex == {{ $index }}" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <span x-show="correctIndex != {{ $index }}" class="text-[9px] font-black text-white/50 group-hover/radio:text-white">TOP</span>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('options')<p class="text-red-500 text-center font-black uppercase tracking-widest text-[9px] italic">{{ $message }}</p>@enderror
            </div>

            <div class="pt-10">
                <button type="submit" :disabled="loading" class="w-full btn-primary px-8 py-6 rounded-3xl font-black text-2xl tracking-tighter uppercase shadow-2xl flex items-center justify-center space-x-4">
                    <span x-show="!loading">Inject Challenge Sequence</span>
                    <span x-show="loading" class="flex items-center space-x-3">
                        <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Transmitting...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
