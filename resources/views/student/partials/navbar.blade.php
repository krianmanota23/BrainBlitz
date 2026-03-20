<nav x-data="{ open: false, profileOpen: false }" class="sticky top-0 z-50 w-full border-b border-white/5 bg-[#0f0f1a]/80 backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo Section -->
            <div class="flex items-center">
                <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black italic text-white shadow-lg group-hover:scale-110 transition-transform">⚡</div>
                    <span class="text-2xl font-black italic tracking-tighter uppercase text-white group-hover:text-gradient transition-colors">BRAIN<span class="text-white">BLITZ</span></span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex ml-12 space-x-8">
                    <a href="{{ route('student.dashboard') }}" 
                       class="text-xs font-black uppercase tracking-widest {{ request()->routeIs('student.dashboard') ? 'text-purple-400' : 'text-gray-500 hover:text-white transition-colors' }}">Dashboard</a>
                    <a href="{{ route('student.join') }}" 
                       class="text-xs font-black uppercase tracking-widest {{ request()->routeIs('student.join') ? 'text-purple-400' : 'text-gray-500 hover:text-white transition-colors' }}">Join Arena</a>
                </div>
            </div>

            <!-- Profile & Menu -->
            <div class="flex items-center space-x-6">
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Blitz Hunter</p>
                        <p class="text-xs font-black italic text-white uppercase">{{ Auth::user()->nickname }}</p>
                    </div>
                    
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition-colors overflow-hidden">
                             <div class="w-full h-full flex items-center justify-center text-xs font-black italic uppercase text-purple-400 bg-purple-400/10">
                                {{ substr(Auth::user()->nickname, 0, 1) }}
                             </div>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="profileOpen" 
                             @click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-100 transform"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75 transform"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-4 w-48 card p-2 shadow-2xl z-50 text-left">
                            <a href="{{ route('profile') }}" class="block px-4 py-3 rounded-lg text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-white/5 hover:text-white transition-all italic">Profile Sync</a>
                            <div class="h-px bg-white/5 my-2"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 rounded-lg text-xs font-black uppercase tracking-widest text-red-500/60 hover:bg-red-500/10 hover:text-red-500 transition-all italic">Exit Arena</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button @click="open = !open" class="md:hidden text-gray-500 hover:text-white">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div x-show="open" class="md:hidden border-t border-white/5 bg-[#0f0f1a] p-4" x-transition>
        <div class="space-y-4">
             <a href="{{ route('student.dashboard') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3">Dashboard</a>
             <a href="{{ route('student.join') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3">Join Arena</a>
             <div class="h-px bg-white/5 my-4"></div>
             <a href="{{ route('profile') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3 italic">Profile Sync</a>
             <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left py-3 text-xs font-black uppercase tracking-widest text-red-500 italic">Exit Arena</button>
             </form>
        </div>
    </div>
</nav>
