@extends('layouts.app')

@section('title', 'Manage Quizzes')

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col p-8 space-y-12 pb-24 max-w-7xl mx-auto w-full">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-none grow"><span class="text-gradient">QUIZ</span><br> ARENA</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4 text-[10px] italic">Command center for your multiplayer challenges.</p>
        </div>
        <div class="flex items-center gap-4">
             <a href="{{ route('admin.quizzes.create') }}" class="btn-primary px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-2xl">Construct New Arena</a>
        </div>
    </div>

    @if($quizzes->isEmpty())
        <div class="card p-32 rounded-[3.5rem] w-full text-center space-y-8 shadow-2xl border-dashed border-2 border-white/5 animate-in zoom-in duration-700">
            <div class="text-9xl grayscale opacity-20">⚡</div>
            <div class="space-y-4">
                <h2 class="text-4xl font-black uppercase text-white italic tracking-tighter">The Arena remains silent</h2>
                <p class="text-gray-500 font-bold max-w-lg mx-auto leading-relaxed text-xs uppercase tracking-widest">You haven't built any battle rooms yet. Construct your first quiz arena and challenge the world!</p>
            </div>
            <a href="{{ route('admin.quizzes.create') }}" class="inline-block px-10 py-5 rounded-xl border border-purple-500/30 text-purple-400 hover:text-white hover:bg-purple-500/20 transition-all font-black uppercase text-xs tracking-[0.3em] italic">Start Building &rarr;</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
            @foreach($quizzes as $quiz)
            <div class="card rounded-[2.5rem] overflow-hidden group hover:border-purple-500/50 shadow-2xl transition-all duration-500 flex flex-col relative">
                <div class="absolute top-0 right-0 p-8 text-7xl font-black italic text-white/5 pointer-events-none select-none group-hover:text-purple-500/10 transition-colors uppercase">{{ substr($quiz->title, 0, 1) }}</div>
                
                <div class="p-10 grow relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg italic
                            @if($quiz->status == 'draft') bg-gray-500/10 text-gray-500 border border-gray-500/20
                            @elseif($quiz->status == 'waiting') bg-blue-500/10 text-blue-400 border border-blue-500/20
                            @elseif($quiz->status == 'ongoing') bg-green-500/10 text-green-400 border border-green-500/20
                            @else bg-purple-500/10 text-purple-400 border border-purple-500/20 @endif">
                            {{ $quiz->status }} Mode
                        </span>
                        <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest italic tracking-[0.2em]">{{ $quiz->topic_mode }} Engine</span>
                    </div>

                    <h3 class="text-3xl font-black uppercase tracking-tighter italic mb-6 group-hover:text-gradient transition-all leading-tight text-white">{{ $quiz->title }}</h3>
                    
                    <div class="flex items-center space-x-6 mb-10">
                        <div class="bg-white/5 py-3 px-6 rounded-xl border border-white/10 group-hover:border-purple-500/30 transition-all">
                            <code class="text-purple-400 font-black tracking-[0.3em] text-lg uppercase">{{ $quiz->room_code }}</code>
                        </div>
                        <div class="text-right">
                             <p class="text-xl font-black italic text-white">{{ $quiz->questions_count }}</p>
                             <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest italic">Challenges</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-8 text-gray-500 text-[10px] font-black uppercase tracking-widest italic">
                         <div class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-2"></span>{{ $quiz->time_per_question }}s Burst</div>
                         <div class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-pink-500 mr-2"></span>{{ $quiz->max_participants }} Slots</div>
                    </div>
                </div>

                {{-- Launch Game Button --}}
                @if($quiz->status == 'draft' && $quiz->questions_count > 0)
                <div class="px-8 pb-2 relative z-10" x-data="{ confirm: false }">
                    <button @click="confirm = true" class="w-full py-4 rounded-xl bg-green-600 hover:bg-green-500 text-white font-black uppercase tracking-widest text-[10px] transition-all shadow-lg">
                        🚀 LAUNCH GAME
                    </button>
                    
                    {{-- Confirmation Modal --}}
                    <template x-if="confirm">
                        <div class="fixed inset-0 bg-black/90 z-[100] flex items-center justify-center p-6" @click.self="confirm = false">
                            <div class="card p-10 rounded-3xl w-full max-w-md shadow-2xl text-center">
                                <div class="text-5xl mb-4">🚀</div>
                                <h3 class="text-2xl font-black uppercase text-green-400 italic tracking-tighter mb-3">Launch this quiz?</h3>
                                <p class="text-gray-400 font-bold text-sm mb-8">Students will be able to join using room code <span class="text-white font-mono">{{ $quiz->room_code }}</span>.</p>
                                <div class="flex space-x-4">
                                    <button @click="confirm = false" class="flex-1 px-6 py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-[10px] text-white">Cancel</button>
                                    <form action="{{ route('admin.quizzes.launch', $quiz->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-6 py-4 rounded-xl bg-green-600 hover:bg-green-500 font-black uppercase tracking-widest text-[10px] text-white shadow-lg">Yes, Launch!</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                @endif

                <div class="p-8 bg-white/5 border-t border-white/10 flex items-center justify-between relative z-10">
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('admin.quizzes.show', $quiz->id) }}" class="text-white hover:text-purple-400 font-black uppercase tracking-widest text-[10px] transition-colors italic">Command Room &rarr;</a>
                        @if($quiz->status == 'finished' && $quiz->latestRoom)
                            <a href="{{ route('admin.rooms.results', $quiz->latestRoom->id) }}" class="text-pink-400 hover:text-pink-300 font-black uppercase tracking-widest text-[10px] transition-colors italic">History 🏆</a>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @if($quiz->status == 'draft')
                            <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="p-2 text-gray-500 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </a>
                            
                            <div x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 text-gray-500 hover:text-red-500 transition-all">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" class="absolute bottom-full right-0 mb-4 w-48 card p-4 shadow-2xl z-20 text-center backdrop-blur-3xl border-red-500/30" x-transition>
                                     <p class="text-[10px] text-gray-400 font-black italic mb-4 uppercase tracking-[0.2em]">Eject Arena?</p>
                                     <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-500 rounded-xl text-white font-black uppercase text-[10px] tracking-widest shadow-xl transition-all">Yes, Delete</button>
                                     </form>
                                </div>
                            </div>
                        @else
                            <div class="px-4 py-1.5 rounded-lg bg-white/5 border border-white/10 flex items-center">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse mr-3"></div>
                                <span class="text-gray-600 font-black uppercase text-[9px] tracking-widest italic">Live Engine</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
