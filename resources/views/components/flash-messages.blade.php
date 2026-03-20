<div class="fixed top-8 right-8 z-[1000] flex flex-col space-y-4 pointer-events-none w-full max-w-sm">
    @foreach (['success', 'error', 'warning', 'info'] as $type)
        @if(session($type))
            <div x-data="{ show: true }" 
                 x-init="setTimeout(() => show = false, 4500)"
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-300 transform"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="pointer-events-auto p-5 rounded-2xl border-l-[6px] shadow-2xl backdrop-blur-xl flex items-center justify-between group
                        @if($type == 'success') bg-green-500/10 border-green-500 @elseif($type == 'error') bg-red-500/10 border-red-500 @elseif($type == 'warning') bg-yellow-500/10 border-yellow-500 @else bg-blue-500/10 border-blue-500 @endif">
                
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl
                                @if($type == 'success') bg-green-500/20 text-green-500 @elseif($type == 'error') bg-red-500/20 text-red-500 @elseif($type == 'warning') bg-yellow-500/20 text-yellow-500 @else bg-blue-500/20 text-blue-500 @endif">
                        @if($type == 'success') 🎨 @elseif($type == 'error') 🚨 @elseif($type == 'warning') ⚠️ @else ℹ️ @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/50">{{ $type }} Message</p>
                        <p class="text-xs font-black uppercase text-white tracking-widest italic">{{ session($type) }}</p>
                    </div>
                </div>

                <button @click="show = false" class="text-white/20 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
    @endforeach

    <!-- Global form validation errors -->
    @if($errors->any())
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 6000)"
             x-show="show" 
             class="pointer-events-auto p-5 rounded-2xl border-l-[6px] bg-red-500/10 border-red-500 shadow-2xl backdrop-blur-xl flex flex-col space-y-2">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-500 flex items-center justify-center text-xl">🚨</div>
                <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Validation Failures</p>
            </div>
            <ul class="text-[9px] font-bold text-gray-500 uppercase list-disc pl-14">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
