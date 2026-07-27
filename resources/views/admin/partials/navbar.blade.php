<nav x-data="{ open: false, profileOpen: false }" class="sticky top-0 z-50 w-full border-b border-white/5 bg-[#0f0f1a]/80 backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo Section -->
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black italic text-white shadow-lg group-hover:scale-110 transition-transform">⚡</div>
                    <span class="text-2xl font-black italic tracking-tighter uppercase text-white group-hover:text-gradient transition-colors">BRAIN<span class="text-white">BLITZ</span></span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex ml-12 space-x-8">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="text-xs font-black uppercase tracking-widest {{ request()->routeIs('admin.dashboard') ? 'text-purple-400' : 'text-gray-500 hover:text-white transition-colors' }}">Dashboard</a>
                    <a href="{{ route('admin.history') }}" 
                       class="text-xs font-black uppercase tracking-widest {{ request()->routeIs('admin.history') ? 'text-purple-400' : 'text-gray-500 hover:text-white transition-colors' }}">🏆 History</a>
                    <a href="{{ route('admin.topics.index') }}" 
                       class="text-xs font-black uppercase tracking-widest {{ request()->routeIs('admin.topics.*') ? 'text-purple-400' : 'text-gray-500 hover:text-white transition-colors' }}">Topics</a>
                    <a href="{{ route('admin.quizzes.index') }}" 
                       class="text-xs font-black uppercase tracking-widest {{ request()->routeIs('admin.quizzes.*') ? 'text-purple-400' : 'text-gray-500 hover:text-white transition-colors' }}">Quizzes</a>
                </div>
            </div>

            <!-- Profile & Menu -->
            <div class="flex items-center space-x-6">
                <div class="hidden md:flex items-center">
                    <div class="relative">
                        <!-- Profile Trigger Button -->
                        <button @click="profileOpen = !profileOpen" 
                                @keydown.escape="profileOpen = false"
                                type="button"
                                class="flex items-center space-x-3 p-2 px-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/30 transition-all duration-200 cursor-pointer group focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest group-hover:text-purple-300 transition-colors">Master Host</p>
                                <p class="text-xs font-black italic text-white uppercase group-hover:text-purple-400 transition-colors">{{ Auth::user()->nickname }}</p>
                            </div>
                            
                            <div class="w-9 h-9 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-xs font-black italic uppercase text-purple-300 group-hover:scale-105 group-hover:border-purple-400 transition-all shadow-md">
                                {{ substr(Auth::user()->nickname, 0, 1) }}
                            </div>

                            <!-- Dropdown Chevron Arrow -->
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-transform duration-200" 
                                 :class="{ 'rotate-180 text-purple-400': profileOpen }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="profileOpen" 
                             x-cloak
                             @click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-150 transform"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100 transform"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="absolute right-0 mt-3 w-52 card p-2.5 rounded-2xl shadow-2xl z-50 text-left border border-white/10 bg-[#16162a]/95 backdrop-blur-xl">
                            <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-gray-300 hover:bg-purple-500/10 hover:text-white transition-all italic">
                                <span class="text-sm">👤</span>
                                <span>Profile Sync</span>
                            </a>
                            <div class="h-px bg-white/10 my-1.5"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-3 text-left px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-red-400 hover:bg-red-500/15 hover:text-red-300 transition-all italic cursor-pointer">
                                    <span class="text-sm">🚪</span>
                                    <span>Exit Arena</span>
                                </button>
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
             <a href="{{ route('admin.dashboard') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3">Dashboard</a>
             <a href="{{ route('admin.history') }}" class="block text-xs font-black uppercase tracking-widest text-purple-400 hover:text-white py-3">🏆 History</a>
             <a href="{{ route('admin.topics.index') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3">Topics</a>
             <a href="{{ route('admin.quizzes.index') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3">Quizzes</a>
             <div class="h-px bg-white/5 my-4"></div>
             <a href="{{ route('profile') }}" class="block text-xs font-black uppercase tracking-widest text-gray-400 hover:text-white py-3 italic">Profile Sync</a>
             <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left py-3 text-xs font-black uppercase tracking-widest text-red-500 italic">Exit Arena</button>
             </form>
        </div>
    </div>
</nav>
