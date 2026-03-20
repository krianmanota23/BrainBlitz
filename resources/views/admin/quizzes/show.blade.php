@extends('layouts.app')

@section('title', 'Arena Detail: ' . $quiz->title)

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col items-center p-6 space-y-12 pb-24 max-w-7xl mx-auto w-full" 
     x-data="{ copied: false, copyCode(code) { navigator.clipboard.writeText(code); this.copied = true; setTimeout(() => this.copied = false, 2000); } }">
    
    <!-- Status Flow Indicator -->
    <div class="w-full flex items-center justify-between px-8 py-6 card rounded-3xl border-white/5 relative overflow-hidden group">
        <div class="absolute inset-x-0 top-1/2 h-1 bg-white/5 -translate-y-1/2 z-0 hidden md:block"></div>
        
        @php
            $statuses = [
                ['id' => 'draft', 'label' => 'Construction', 'icon' => '🏗️'],
                ['id' => 'waiting', 'label' => 'Lobby Open', 'icon' => '🚪'],
                ['id' => 'ongoing', 'label' => 'Battle Live', 'icon' => '⚔️'],
                ['id' => 'finished', 'label' => 'concluded', 'icon' => '🏁'],
            ];
            $currentStatusIndex = array_search($quiz->status, array_column($statuses, 'id'));
        @endphp

        @foreach($statuses as $index => $step)
            <div class="relative z-10 flex flex-col items-center space-y-3 group/step">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl transition-all duration-500 border-2
                            @if($index <= $currentStatusIndex) bg-purple-500 border-purple-400 text-white shadow-[0_0_20px_rgba(168,85,247,0.4)] @else bg-[#1a1a2e] border-white/10 text-gray-600 @endif">
                    {{ $step['icon'] }}
                </div>
                <div class="text-center">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] {{ $index <= $currentStatusIndex ? 'text-purple-400' : 'text-gray-600' }}">{{ $step['label'] }}</p>
                    @if($index == $currentStatusIndex)
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse mt-1"></span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($quiz->questions_count == 0)
    <div class="w-full p-6 bg-yellow-500/10 border border-yellow-500/20 rounded-2xl flex items-center space-x-6 animate-pulse">
        <span class="text-3xl">⚠️</span>
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-yellow-500">Zero-Question Arena Warning</p>
            <p class="text-[10px] font-bold text-gray-400 mt-1 italic uppercase tracking-widest">Add at least one challenge before launching the session to students.</p>
        </div>
    </div>
    @endif

    <!-- Header/Arena Info -->
    <div class="w-full flex flex-col md:flex-row items-center justify-between mb-8 gap-8">
        <div class="flex-1">
            <h1 class="text-7xl font-black italic tracking-tighter uppercase leading-none text-gradient">{{ $quiz->title }}</h1>
            <div class="flex items-center space-x-8 mt-10">
                 <div class="flex flex-col">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Time Limit</span>
                    <span class="text-xl font-black tracking-tighter text-white">{{ $quiz->time_per_question }}s</span>
                 </div>
                 <div class="flex flex-col border-l border-white/10 pl-8">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Max Battle</span>
                    <span class="text-xl font-black tracking-tighter text-white">{{ $quiz->max_participants }}p</span>
                 </div>
                 <div class="flex flex-col border-l border-white/10 pl-8">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Topics</span>
                    <span class="text-xl font-black tracking-tighter text-white">{{ $quiz->topic_mode }} engine</span>
                 </div>
            </div>
        </div>

        @if($quiz->status == 'draft' && $quiz->questions_count > 0)
            <div x-data="{ confirmingLaunch: false }">
                <button @click="confirmingLaunch = true" class="px-12 py-6 rounded-2xl bg-green-600 hover:bg-green-500 text-white font-black text-xl tracking-tighter uppercase shadow-[0_0_40px_rgba(22,163,74,0.3)] hover:scale-105 transform transition-all">
                    🚀 Launch Game &rarr;
                </button>

                <template x-if="confirmingLaunch">
                    <div class="fixed inset-0 bg-black/95 z-[100] flex items-center justify-center p-6 backdrop-blur-md" @click.self="confirmingLaunch = false">
                        <div class="card p-12 rounded-[3rem] w-full max-w-lg shadow-2xl text-center border-green-500/30">
                            <div class="text-6xl mb-4">🚀</div>
                            <h3 class="text-4xl font-black uppercase text-green-400 italic tracking-tighter mb-4">Launch this quiz?</h3>
                            <p class="text-gray-400 font-bold mb-10 text-sm">Students will be able to join using code <span class="text-white font-mono border-b border-white">{{ $quiz->room_code }}</span>.</p>
                            <div class="flex space-x-4">
                                <button @click="confirmingLaunch = false" class="flex-1 px-8 py-5 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-[10px] text-white">Cancel</button>
                                <form action="{{ route('admin.quizzes.launch', $quiz->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full px-8 py-5 rounded-xl bg-green-600 hover:bg-green-500 font-black uppercase tracking-widest text-[10px] text-white shadow-lg">Yes, Launch!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @elseif($quiz->status == 'draft' && $quiz->questions_count == 0)
            <div class="relative group">
                <button disabled class="px-12 py-6 rounded-2xl bg-gray-700 text-gray-400 font-black text-xl tracking-tighter uppercase cursor-not-allowed opacity-50">
                    🚀 Launch Game
                </button>
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap text-[10px] font-black text-yellow-500 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                    ⚠️ Add questions first
                </div>
            </div>
        @endif

        @if($quiz->status == 'finished')
             <div class="flex flex-col items-center" x-data="{ confirmRelaunch: false }">
                 <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-4 italic">Battle Concluded</p>
                 <button @click="confirmRelaunch = true" class="px-8 py-4 rounded-xl bg-green-600 hover:bg-green-500 text-white font-black uppercase tracking-widest text-[10px] shadow-xl transition-all">Re-launch Arena</button>
                 
                 <template x-if="confirmRelaunch">
                     <div class="fixed inset-0 bg-black/90 z-[100] flex items-center justify-center p-6" @click.self="confirmRelaunch = false">
                         <div class="card p-10 rounded-3xl w-full max-w-md shadow-2xl text-center">
                             <h3 class="text-2xl font-black uppercase text-green-400 italic tracking-tighter mb-3">Re-launch this quiz?</h3>
                             <p class="text-gray-400 font-bold text-sm mb-8">A new room will be created for students to join.</p>
                             <div class="flex space-x-4">
                                 <button @click="confirmRelaunch = false" class="flex-1 px-6 py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-[10px] text-white">Cancel</button>
                                 <form action="{{ route('admin.quizzes.launch', $quiz->id) }}" method="POST" class="flex-1">
                                     @csrf
                                     <button type="submit" class="w-full px-6 py-4 rounded-xl bg-green-600 hover:bg-green-500 font-black uppercase tracking-widest text-[10px] text-white">Yes, Re-launch!</button>
                                 </form>
                             </div>
                         </div>
                     </div>
                 </template>
             </div>
        @endif

        <!-- Room Code Spotlight -->
        <div class="card p-8 rounded-3xl flex flex-col items-center text-center shadow-2xl border-purple-500/30 relative overflow-hidden">
             <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-pink-600/5 pointer-events-none"></div>
             <div @click="copyCode('{{ $quiz->room_code }}')" class="cursor-pointer group relative z-10">
                 <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Invite Command Code</span>
                 <p class="text-6xl font-mono font-black tracking-[0.2em] text-white group-hover:text-purple-400 transition-colors uppercase select-none">{{ $quiz->room_code }}</p>
                 <div class="mt-4 flex flex-col items-center">
                    <span class="text-[9px] font-bold text-gray-600 uppercase tracking-widest group-hover:text-gray-400 transition-all italic">Click to clone to clipboard</span>
                    <div x-show="copied" x-transition class="mt-2 text-[10px] font-black text-green-500 uppercase tracking-widest">COPIED SUCCESSFULLY! ✓</div>
                 </div>
             </div>
        </div>
    </div>

    <!-- Questions Container -->
    <div class="w-full flex flex-col space-y-12">
        <div class="flex items-center justify-between border-b-2 border-white/10 pb-6 px-4">
             <div class="flex items-center space-x-4">
                 <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-xl shadow-inner">🧬</div>
                 <h2 class="text-3xl font-black uppercase italic tracking-tighter">CHALLENGE <span class="text-gradient">SEQUENCE</span></h2>
             </div>
             @if($quiz->status == 'draft')
             <a href="{{ route('admin.quizzes.questions.create', $quiz->id) }}" class="px-8 py-4 rounded-xl btn-primary transition-all font-black text-xs tracking-widest uppercase shadow-lg text-white">+ Add Question</a>
             @endif
        </div>

        @if($quiz->questions->isEmpty())
             <div class="card p-24 rounded-[3.5rem] text-center space-y-8 animate-in zoom-in duration-500 border-dashed border-2 border-white/5">
                 <div class="text-8xl grayscale opacity-30 animate-pulse">🧩</div>
                 <div class="space-y-2">
                    <p class="text-gray-400 font-black uppercase tracking-[0.3em] text-sm">NO CHALLENGES CONSTRUCTED</p>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-widest italic">Prepare your first tactical challenge below.</p>
                 </div>
                 @if($quiz->status == 'draft')
                 <a href="{{ route('admin.quizzes.questions.create', $quiz->id) }}" class="inline-block px-10 py-4 rounded-xl border border-purple-500/30 text-purple-400 hover:text-white hover:bg-purple-500/20 transition-all font-black uppercase text-[10px] tracking-widest italic tracking-widest">Inject First Challenge &rarr;</a>
                 @endif
             </div>
        @else
             <div class="grid grid-cols-1 gap-8">
                @foreach($quiz->questions->sortBy('order_number') as $index => $question)
                <div class="card p-10 rounded-[2.5rem] shadow-xl group border-l-[6px] border-l-gray-600/30 hover:border-l-purple-500 transition-all relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 text-8xl font-black italic text-white/5 pointer-events-none select-none">Q{{ $question->order_number }}</div>
                    
                    <div class="flex flex-col md:flex-row gap-12 relative z-10">
                         <!-- Order Controls (Exclusive to Draft) -->
                         @if($quiz->status == 'draft')
                         <div class="flex flex-row md:flex-col items-center justify-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 self-center">
                            <form action="{{ route('admin.quizzes.questions.reorder', [$quiz->id, $question->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" @if($question->order_number == 1) disabled @endif 
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all bg-white/5 border border-white/10 hover:bg-white/20 disabled:opacity-10 text-xl">▲</button>
                            </form>
                            <span class="text-lg font-black italic text-purple-400 tabular-nums">{{ $question->order_number }}</span>
                            <form action="{{ route('admin.quizzes.questions.reorder', [$quiz->id, $question->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" @if($question->order_number == $quiz->questions_count) disabled @endif 
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all bg-white/5 border border-white/10 hover:bg-white/20 disabled:opacity-10 text-xl">▼</button>
                            </form>
                         </div>
                         @endif

                         <div class="flex-1">
                            <div class="flex items-center space-x-6 mb-6">
                                 <span class="text-[9px] font-black text-purple-400 bg-purple-400/10 px-4 py-1.5 rounded-lg uppercase tracking-widest border border-purple-500/30 italic">Category: {{ $question->topic->name }}</span>
                                 <span class="text-[9px] font-black text-gray-500 bg-white/5 px-4 py-1.5 rounded-lg uppercase tracking-widest border border-white/10 italic">{{ $question->time_limit }}s Limit</span>
                            </div>
                            <h3 class="text-3xl font-black tracking-tighter mb-10 leading-snug text-white group-hover:text-purple-400 transition-colors">{{ $question->question_text }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($question->options as $option)
                                <div class="px-6 py-4 rounded-2xl border flex items-center justify-between transition-all group/opt
                                    @if($option->color == 'red') border-red-500/20 bg-red-500/5 @elseif($option->color == 'blue') border-blue-500/20 bg-blue-500/5 @elseif($option->color == 'yellow') border-yellow-500/20 bg-yellow-500/5 @else border-green-500/20 bg-green-500/5 @endif
                                    @if($option->is_correct) border-green-500/50 bg-green-500/10 ring-1 ring-green-500/30 @endif">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-4 h-4 rounded-full shadow-lg
                                            @if($option->color == 'red') bg-red-500 @elseif($option->color == 'blue') bg-blue-500 @elseif($option->color == 'yellow') bg-yellow-500 @else bg-green-500 @endif"></div>
                                        <span class="text-sm font-bold @if($option->is_correct) text-white @else text-gray-500 @endif">{{ $option->option_text }}</span>
                                    </div>
                                    @if($option->is_correct)
                                        <div class="bg-green-500/20 p-1.5 rounded-lg">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                         </div>

                         @if($quiz->status == 'draft')
                         <!-- Question Actions -->
                         <div class="flex md:flex-col justify-center space-y-0 md:space-y-4 space-x-4 md:space-x-0">
                            <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="px-8 py-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-white transition-all font-black text-[10px] tracking-widest uppercase text-center flex items-center">
                                 SYNC
                            </a>
                            
                            <div x-data="{ confirming: false }" class="w-full">
                                <button @click="confirming = true" class="w-full px-8 py-4 rounded-xl bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white transition-all font-black text-[10px] tracking-widest uppercase text-center">EJECT</button>
                                
                                <template x-if="confirming">
                                    <div class="fixed inset-0 bg-black/95 z-[100] flex items-center justify-center p-6 backdrop-blur-xl">
                                        <div class="card p-12 rounded-[3.5rem] w-full max-w-md shadow-2xl text-center border-red-500/30">
                                            <div class="text-6xl mb-6">🗑️</div>
                                            <h3 class="text-3xl font-black uppercase text-red-500 italic tracking-tighter mb-4">REMOVE CHALLENGE #{{ $question->order_number }}?</h3>
                                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-10 italic">This data will be purged from the arena sequence indefinitely.</p>
                                            <div class="flex space-x-4">
                                                <button @click="confirming = false" class="flex-1 px-8 py-4 rounded-xl border border-white/10 hover:bg-white/5 font-black uppercase tracking-widest text-[10px]">Abort</button>
                                                <form action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full px-8 py-4 rounded-xl bg-red-600 hover:bg-red-700 font-black uppercase tracking-widest text-[10px] text-white">Purge Challenge</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                         </div>
                         @endif
                    </div>
                </div>
                @endforeach
             </div>
        @endif
    </div>
</div>
@endsection
