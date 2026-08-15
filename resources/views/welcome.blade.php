<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'PromptLib') }} — Koleksi Prompt AI Terbaik</title>
        <meta name="description" content="Koleksi prompt AI berkualiti tinggi untuk Midjourney, DALL-E, dan Stable Diffusion. Cari, salin dan guna terus.">
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎨</text></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS & Alpine.js -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: { sans: ['Inter', 'sans-serif'] },
                        colors: {
                            primary: '#6d28d9',
                            secondary: '#be185d',
                        },
                        animation: {
                            'blob': 'blob 7s infinite',
                            'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        },
                        keyframes: {
                            blob: {
                                '0%': { transform: 'translate(0px, 0px) scale(1)' },
                                '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                                '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                                '100%': { transform: 'translate(0px, 0px) scale(1)' },
                            },
                            fadeInUp: {
                                '0%': { opacity: '0', transform: 'translateY(20px)' },
                                '100%': { opacity: '1', transform: 'translateY(0)' },
                            }
                        }
                    }
                }
            }
        </script>

        <style>
            body {
                background-color: #0f172a;
                color: #f8fafc;
                min-height: 100vh;
                overflow-x: hidden;
            }
            .glass-card {
                background: rgba(30, 41, 59, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            }
            .text-gradient {
                background: linear-gradient(to right, #a855f7, #ec4899);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .blur-content {
                filter: blur(8px);
                user-select: none;
                pointer-events: none;
            }
            [x-cloak] { display: none !important; }

            .prompt-card { opacity: 0; }
            .prompt-card.visible { animation: fadeInUp 0.6s ease-out forwards; }

            /* Hide scrollbar for category pills on mobile */
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

            /* Skeleton loading */
            @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
            .skeleton { background: linear-gradient(90deg, rgba(51,65,85,0.3) 25%, rgba(51,65,85,0.6) 50%, rgba(51,65,85,0.3) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 1rem; }

            /* Custom scrollbar */
            ::-webkit-scrollbar { width: 8px; }
            ::-webkit-scrollbar-track { background: #0f172a; }
            ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #475569; }
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 2px; }

            /* 3D Card Flip */
            .flip-container {
                perspective: 1200px;
                aspect-ratio: 9 / 16;
            }
            .flip-card {
                position: relative;
                width: 100%;
                height: 100%;
                transition: transform 0.7s cubic-bezier(0.4, 0.0, 0.2, 1);
                transform-style: preserve-3d;
            }
            .flip-container.flipped .flip-card {
                transform: rotateY(180deg);
            }
            /* Desktop hover */
            @media (hover: hover) and (pointer: fine) {
                .flip-container:hover .flip-card {
                    transform: rotateY(180deg);
                }
            }
            .flip-front, .flip-back {
                position: absolute;
                inset: 0;
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
                border-radius: 1rem;
                overflow: hidden;
            }
            .flip-front {
                z-index: 2;
            }
            .flip-back {
                transform: rotateY(180deg);
                z-index: 1;
            }
        </style>
    </head>
    <body class="antialiased relative selection:bg-primary selection:text-white">
        
        <!-- Background Orbs -->
        <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-pink-600 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-indigo-600 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob" style="animation-delay: 4s;"></div>
        </div>

        <!-- Navigation -->
        <nav class="glass-card fixed w-full z-[110] transition-all duration-300" x-data="{ mobileOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <svg class="w-8 h-8 text-pink-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                        <span class="text-xl font-bold tracking-tight text-white">Prompt<span class="text-pink-500">Lib</span></span>
                    </a>
                    <!-- Desktop Nav -->
                    <div class="hidden sm:flex items-center gap-2">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-300 hover:text-white transition px-4 py-2 rounded-lg hover:bg-white/10 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path></svg>
                                    Dashboard
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.prompts.create') }}" class="text-sm font-medium text-green-400 hover:text-green-300 transition px-3 py-2 rounded-lg hover:bg-green-500/10 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-cyan-400 hover:text-cyan-300 transition px-3 py-2 rounded-lg hover:bg-cyan-500/10 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Pengguna
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-red-400 hover:text-red-300 transition px-3 py-2 rounded-lg hover:bg-red-500/10 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Log Keluar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition px-4 py-2 rounded-lg hover:bg-white/10">Log Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm font-medium bg-gradient-to-r from-purple-600 to-pink-600 px-5 py-2.5 rounded-lg hover:opacity-90 transition text-white shadow-lg shadow-purple-500/30">Daftar Akaun</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                    <!-- Mobile Hamburger -->
                    <button @click="mobileOpen = !mobileOpen" class="sm:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-cloak x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <!-- Mobile Menu -->
                <div x-cloak x-show="mobileOpen" x-transition class="sm:hidden pb-4 space-y-1 border-t border-white/10 mt-2 pt-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block text-sm font-medium text-gray-300 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition">📊 Dashboard</a>
                            <a href="{{ route('home') }}" class="block text-sm font-medium text-gray-300 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition">🏠 Laman Utama</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.prompts.create') }}" class="block text-sm font-medium text-green-400 px-3 py-2 rounded-lg hover:bg-green-500/10 transition">➕ Tambah Prompt</a>
                                <a href="{{ route('admin.users.index') }}" class="block text-sm font-medium text-cyan-400 px-3 py-2 rounded-lg hover:bg-cyan-500/10 transition">👥 Urus Pengguna</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left text-sm font-medium text-red-400 px-3 py-2 rounded-lg hover:bg-red-500/10 transition">🚪 Log Keluar</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block text-sm font-medium text-gray-300 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition">🔑 Log Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block text-sm font-medium text-pink-400 px-3 py-2 rounded-lg hover:bg-pink-500/10 transition">✨ Daftar Akaun</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Expiry Warning for Welcome Page -->
        @auth
            @if(auth()->user()->isSubscriptionExpiringSoon())
                <!-- Countdown Logic for Welcome Page -->
                <div x-data="{ 
                        showWarning: true, open: true,
                        expiryTime: new Date('{{ auth()->user()->premium_expires_at->toIso8601String() }}').getTime(),
                        days: 0, hours: 0, minutes: 0, seconds: 0,
                        countdown() {
                            let now = new Date().getTime();
                            let distance = this.expiryTime - now;
                            if(distance < 0) {
                                window.location.reload();
                                return;
                            }
                            this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        }
                    }" x-init="countdown(); setInterval(() => countdown(), 1000);">
                    
                    <!-- Desktop Top Banner -->
                    <div class="hidden sm:block pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-show="showWarning" x-transition>
                        <div class="bg-gradient-to-r from-orange-500/10 to-yellow-500/10 border border-orange-500/30 rounded-2xl p-4 shadow-lg shadow-orange-500/5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1 shrink-0">
                                    <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                        <span class="text-sm font-extrabold text-orange-500" x-text="days"></span>
                                        <span class="text-[8px] font-bold text-orange-400">HARI</span>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                        <span class="text-sm font-extrabold text-orange-500" x-text="hours"></span>
                                        <span class="text-[8px] font-bold text-orange-400">JAM</span>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                        <span class="text-sm font-extrabold text-orange-500" x-text="minutes"></span>
                                        <span class="text-[8px] font-bold text-orange-400">MIN</span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white mb-0.5">⚠️ Langganan Hampir Tamat</h3>
                                    <p class="text-xs text-orange-200/80">Premium akan ditamatkan. Segera perbaharui.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pricing.index') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-yellow-500 text-black text-xs font-bold shadow-lg hover:opacity-90 transition whitespace-nowrap">
                                    Perbaharui Semula
                                </a>
                                <button @click="showWarning = false" class="p-2 rounded-xl bg-white/5 text-gray-400 hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Popup (Bottom Sheet) -->
                    <div class="sm:hidden">
                        <template x-teleport="body">
                            <div x-show="open" x-cloak class="fixed inset-0 z-[100]" @click.self="open = false">
                                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false" x-transition.opacity></div>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" 
                                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                                     class="fixed bottom-0 left-0 right-0 bg-gradient-to-b from-gray-800 to-gray-900 rounded-t-3xl p-5 pb-8 z-[101] border-t-2 border-orange-500/50 shadow-[0_-10px_40px_rgba(249,115,22,0.15)]">
                                    
                                    <div class="w-12 h-1.5 bg-gray-600/50 rounded-full mx-auto mb-6"></div>
                                    
                                    <div class="flex flex-col items-center text-center">
                                        <h3 class="text-xl font-extrabold text-white mb-2 text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400">Langganan Hampir Tamat!</h3>
                                        <p class="text-sm text-gray-300 mb-4">Akses Premium anda akan tamat dalam masa:</p>
                                        
                                        <div class="flex gap-2 mb-6">
                                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                                <span class="text-lg font-extrabold text-orange-500" x-text="days"></span>
                                                <span class="text-[10px] font-bold text-orange-400">HARI</span>
                                            </div>
                                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                                <span class="text-lg font-extrabold text-orange-500" x-text="hours"></span>
                                                <span class="text-[10px] font-bold text-orange-400">JAM</span>
                                            </div>
                                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                                <span class="text-lg font-extrabold text-orange-500" x-text="minutes"></span>
                                                <span class="text-[10px] font-bold text-orange-400">MIN</span>
                                            </div>
                                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                                <span class="text-lg font-extrabold text-orange-500" x-text="seconds"></span>
                                                <span class="text-[10px] font-bold text-orange-400">SAAT</span>
                                            </div>
                                        </div>
                                        
                                        <div class="w-full space-y-3">
                                            <a href="{{ route('pricing.index') }}" class="block w-full py-3.5 rounded-xl bg-gradient-to-r from-orange-500 to-yellow-500 text-black text-sm font-extrabold shadow-lg shadow-orange-500/20 hover:scale-[1.02] transition-transform">
                                                🌟 Perbaharui Sekarang
                                            </a>
                                            <button @click="open = false" class="block w-full py-3 rounded-xl bg-white/5 text-gray-400 text-sm font-semibold hover:bg-white/10 transition">
                                                Ingatkan Saya Nanti
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Hero Section -->
        <div class="pt-24 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 animate-fade-in-up">
                Koleksi <span class="text-gradient">Prompt AI</span><br />Yang Terbaik
            </h1>
            <p class="text-lg md:text-xl text-gray-400 max-w-2xl mx-auto mb-10 animate-fade-in-up" style="animation-delay: 0.1s;">
                Cari, salin dan gunakan prompt berkualiti tinggi untuk Midjourney, DALL-E, Stable Diffusion dan banyak lagi.
            </p>

            <!-- Stats -->
            <div class="flex flex-wrap justify-center gap-4 sm:gap-6 mb-12 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="glass-card rounded-2xl px-6 py-4 text-center min-w-[120px]">
                    <div class="text-3xl font-extrabold text-white">{{ number_format($totalPrompts) }}</div>
                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Jumlah Prompt</div>
                </div>
                <div class="glass-card rounded-2xl px-6 py-4 text-center min-w-[120px]">
                    <div class="text-3xl font-extrabold text-green-400">{{ number_format($freePrompts) }}</div>
                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Percuma</div>
                </div>
                <div class="glass-card rounded-2xl px-6 py-4 text-center min-w-[120px]">
                    <div class="text-3xl font-extrabold text-yellow-400">{{ number_format($premiumPrompts) }}</div>
                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Premium</div>
                </div>
                <div class="glass-card rounded-2xl px-6 py-4 text-center min-w-[120px] bg-purple-500/5 border-purple-500/20">
                    <div class="text-3xl font-extrabold text-purple-400 flex items-center justify-center gap-1.5">
                        <svg class="w-6 h-6 text-purple-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        {{ number_format($totalVisitors) }}
                    </div>
                    <div class="text-xs text-purple-300/80 mt-1 uppercase tracking-wider font-medium">Pelawat</div>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <form method="GET" action="{{ route('home') }}" class="max-w-3xl mx-auto animate-fade-in-up relative z-[80]" style="animation-delay: 0.3s;">
                <!-- Preserve existing filter params -->
                @if(request('category') && request('category') !== 'all')
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('rating') && request('rating') !== 'all')
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                @endif
                @if(request('tag'))
                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                @endif

                <div class="glass-card rounded-2xl p-2.5" style="overflow: visible;">
                    <!-- Search Row -->
                    <div class="flex gap-2">
                        <div class="relative flex-grow">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search" id="globalSearch" value="{{ request('search') }}" placeholder="Cari prompt, tag, kategori..."
                                class="w-full bg-white/10 border-0 rounded-xl pl-12 pr-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:bg-white/15 transition text-sm">
                        </div>
                        <button type="submit" class="px-5 py-3 rounded-xl text-sm font-bold bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:opacity-90 transition shadow-lg shadow-purple-500/20 shrink-0 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                    <!-- Filter Row -->
                    <div class="flex flex-wrap items-center gap-2 mt-2.5 pt-2.5 border-t border-white/5">
                        <!-- Tier Filter -->
                        <div class="flex gap-1 flex-shrink-0">
                            <a href="{{ route('home', array_merge(request()->except('filter', 'page'), [])) }}" class="filter-link px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request('filter', 'all') === 'all' ? 'bg-white/20 text-white' : 'text-gray-500 hover:text-white hover:bg-white/10' }}">Semua</a>
                            <a href="{{ route('home', array_merge(request()->except('page'), ['filter' => 'free'])) }}" class="filter-link px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request('filter') === 'free' ? 'bg-green-500/20 text-green-400' : 'text-gray-500 hover:text-green-400 hover:bg-green-500/10' }}">Free</a>
                            <a href="{{ route('home', array_merge(request()->except('page'), ['filter' => 'premium'])) }}" class="filter-link px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request('filter') === 'premium' ? 'bg-yellow-500/20 text-yellow-400' : 'text-gray-500 hover:text-yellow-400 hover:bg-yellow-500/10' }}">⭐ Premium</a>
                        </div>

                        <div class="hidden sm:block w-px h-5 bg-white/10"></div>

                        <!-- Star Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition border {{ request('rating') ? 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30' : 'text-gray-500 hover:text-white border-white/10 hover:bg-white/5' }}">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ request('rating') ? request('rating') . ' Bintang' : 'Rating' }}
                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                             <!-- Desktop dropdown -->
                            <div x-show="open" x-cloak @click.away="open = false" x-transition class="hidden sm:block absolute left-0 mt-2 w-44 bg-gray-800 rounded-xl shadow-2xl ring-1 ring-white/10 p-1.5 z-[100]">
                                <a href="{{ route('home', array_merge(request()->except('rating', 'page'), [])) }}" class="filter-link flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition {{ !request('rating') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                                    Semua Rating
                                </a>
                                @for($s = 5; $s >= 1; $s--)
                                    <a href="{{ route('home', array_merge(request()->except('page'), ['rating' => $s])) }}" class="filter-link flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition {{ request('rating') == $s ? 'bg-yellow-500/15 text-yellow-400' : 'text-gray-400 hover:bg-white/5 hover:text-yellow-400' }}">
                                        <span class="text-yellow-400">@for($i = 0; $i < $s; $i++)★@endfor</span>
                                        <span class="text-gray-500 ml-auto">{{ $s }}/5</span>
                                    </a>
                                @endfor
                            </div>
                            <!-- Mobile bottom sheet -->
                            <template x-teleport="body">
                                <div x-show="open" x-cloak class="sm:hidden fixed inset-0 z-[100]" @click.self="open = false">
                                    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
                                    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                                         class="fixed bottom-0 left-0 right-0 bg-gray-800 rounded-t-2xl p-4 pb-8 z-[101] border-t border-white/10">
                                        <div class="w-10 h-1 bg-gray-600 rounded-full mx-auto mb-4"></div>
                                        <h3 class="text-sm font-bold text-white mb-3 text-center">⭐ Pilih Rating</h3>
                                        <div class="space-y-1">
                                            <a href="{{ route('home', array_merge(request()->except('rating', 'page'), [])) }}" class="filter-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ !request('rating') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5' }}">
                                                Semua Rating
                                            </a>
                                            @for($s = 5; $s >= 1; $s--)
                                                <a href="{{ route('home', array_merge(request()->except('page'), ['rating' => $s])) }}" class="filter-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ request('rating') == $s ? 'bg-yellow-500/15 text-yellow-400' : 'text-gray-400 hover:bg-white/5' }}">
                                                    <span class="text-yellow-400">@for($i = 0; $i < $s; $i++)★@endfor</span>
                                                    <span class="text-gray-500 ml-auto">{{ $s }}/5</span>
                                                </a>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Tag Dropdown -->
                        @if(count($allTags) > 0)
                        <div class="relative" x-data="{ open: false, search: '' }">
                            <button type="button" @click="open = !open" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition border {{ request('tag') ? 'bg-pink-500/15 text-pink-400 border-pink-500/30' : 'text-gray-500 hover:text-white border-white/10 hover:bg-white/5' }}">
                                {{ request('tag') ? request('tag') : 'Tag' }}
                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <!-- Desktop dropdown -->
                            <div x-show="open" x-cloak @click.away="open = false" x-transition class="hidden sm:block absolute left-0 mt-2 w-52 bg-gray-800 rounded-xl shadow-2xl ring-1 ring-white/10 p-1.5 z-[100]">
                                <div class="px-2 pb-1.5 mb-1 border-b border-white/5">
                                    <input type="text" x-model="search" placeholder="Cari tag..." class="w-full bg-gray-900/50 border-0 rounded-lg px-3 py-1.5 text-xs text-white placeholder-gray-500 focus:ring-1 focus:ring-pink-500">
                                </div>
                                <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                    <a href="{{ route('home', array_merge(request()->except('tag', 'page'), [])) }}" class="filter-link flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition {{ !request('tag') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                                        Semua Tag
                                    </a>
                                    @foreach($allTags as $tag => $tagCount)
                                        <a href="{{ route('home', array_merge(request()->except('page'), ['tag' => $tag])) }}"
                                           x-show="!search || '{{ strtolower($tag) }}'.includes(search.toLowerCase())"
                                           class="filter-link flex items-center justify-between px-3 py-2 rounded-lg text-xs transition {{ request('tag') === $tag ? 'bg-pink-500/15 text-pink-400' : 'text-gray-400 hover:bg-white/5 hover:text-pink-400' }}">
                                            <span>{{ $tag }}</span>
                                            <span class="text-[10px] text-gray-600">({{ $tagCount }})</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Mobile bottom sheet -->
                            <template x-teleport="body">
                                <div x-show="open" x-cloak class="sm:hidden fixed inset-0 z-[100]" @click.self="open = false">
                                    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
                                    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                                         class="fixed bottom-0 left-0 right-0 bg-gray-800 rounded-t-2xl p-4 pb-8 z-[101] border-t border-white/10 max-h-[70vh] flex flex-col">
                                        <div class="w-10 h-1 bg-gray-600 rounded-full mx-auto mb-4 shrink-0"></div>
                                        <h3 class="text-sm font-bold text-white mb-3 text-center shrink-0">Pilih Tag</h3>
                                        <div class="px-1 pb-2 mb-1 border-b border-white/5 shrink-0">
                                            <input type="text" x-model="search" placeholder="Cari tag..." class="w-full bg-gray-900/50 border-0 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-1 focus:ring-pink-500">
                                        </div>
                                        <div class="overflow-y-auto flex-1 space-y-1 custom-scrollbar">
                                            <a href="{{ route('home', array_merge(request()->except('tag', 'page'), [])) }}" class="filter-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition {{ !request('tag') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5' }}">
                                                Semua Tag
                                            </a>
                                            @foreach($allTags as $tag => $tagCount)
                                                <a href="{{ route('home', array_merge(request()->except('page'), ['tag' => $tag])) }}"
                                                   x-show="!search || '{{ strtolower($tag) }}'.includes(search.toLowerCase())"
                                                   class="filter-link flex items-center justify-between px-4 py-3 rounded-xl text-sm transition {{ request('tag') === $tag ? 'bg-pink-500/15 text-pink-400' : 'text-gray-400 hover:bg-white/5 hover:text-pink-400' }}">
                                                    <span>{{ $tag }}</span>
                                                    <span class="text-xs text-gray-600">({{ $tagCount }})</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        @endif

                        <!-- Active filter chips inline -->
                        @if(request('rating'))
                            <a href="{{ route('home', request()->except('rating', 'page')) }}" class="filter-link flex items-center gap-1 px-2.5 py-1 rounded-full text-xs bg-yellow-500/15 text-yellow-400 hover:bg-yellow-500/25 transition">
                                @for($i = 0; $i < request('rating'); $i++)★@endfor ✕
                            </a>
                        @endif
                        @if(request('tag'))
                            <a href="{{ route('home', request()->except('tag', 'page')) }}" class="filter-link flex items-center gap-1 px-2.5 py-1 rounded-full text-xs bg-pink-500/15 text-pink-400 hover:bg-pink-500/25 transition">
                                {{ request('tag') }} ✕
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Active Filters Summary -->
                @if(request('search') || request('filter') || request('rating') || request('tag') || request('category'))
                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        <span class="text-sm text-gray-400 font-medium">{{ $prompts->total() }} hasil ditemui</span>
                        @if(request('search') || request('filter') || request('rating') || request('tag'))
                            <a href="{{ route('home') }}" class="filter-link inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium bg-red-500/15 text-red-400 hover:bg-red-500/25 border border-red-500/20 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Semua
                            </a>
                        @endif
                    </div>
                @endif
            </form>

            <!-- Category Pills - horizontal scroll on mobile -->
            <div class="mt-6 animate-fade-in-up relative z-10 -mx-4 px-4 sm:mx-0 sm:px-0" style="animation-delay: 0.4s;">
                <div class="flex sm:flex-wrap sm:justify-center gap-2 overflow-x-auto pb-2 sm:pb-0 no-scrollbar">
                    <a href="{{ route('home', array_merge(request()->except('category', 'page'), [])) }}" class="filter-link px-3.5 py-1.5 rounded-full text-xs font-medium transition whitespace-nowrap shrink-0 {{ !request('category') || request('category') === 'all' ? 'bg-white/20 text-white ring-1 ring-white/30' : 'text-gray-400 hover:text-white hover:bg-white/10 border border-white/5' }}">
                        Semua
                    </a>
                    @foreach($categories as $key => $cat)
                        @php $count = $categoryCounts[$key] ?? 0; @endphp
                        @if($count > 0)
                            <a href="{{ route('home', array_merge(request()->except('page'), ['category' => $key])) }}" class="filter-link px-3.5 py-1.5 rounded-full text-xs font-medium transition flex items-center gap-1 whitespace-nowrap shrink-0 {{ request('category') === $key ? 'bg-white/20 text-white ring-1 ring-white/30' : 'text-gray-400 hover:text-white hover:bg-white/10 border border-white/5' }}">
                                {{ $cat['icon'] }} {{ $cat['label'] }}
                                <span class="text-[10px] opacity-60">({{ $count }})</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Prompt Grid -->
        <div id="prompt-results" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            
            @if($prompts->isEmpty())
                <div class="text-center text-gray-400 py-20 glass-card rounded-2xl">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xl font-bold text-gray-300 mb-2">Tiada prompt ditemui</p>
                    <p class="text-sm text-gray-500">Cuba kata kunci lain atau reset penapis anda.</p>
                    @if(request('search') || request('filter'))
                        <a href="{{ route('home') }}" class="mt-4 inline-flex items-center gap-1 text-pink-400 hover:text-pink-300 transition text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Lihat semua prompt
                        </a>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($prompts as $index => $prompt)
                    @php
                        $isPremium = $prompt->is_premium;
                        $hasAccess = true;
                        if ($isPremium) {
                            if (!auth()->check() || (!auth()->user()->isPremiumActive() && auth()->user()->role !== 'admin')) {
                                $hasAccess = false;
                            }
                        }
                    @endphp

                    <div class="prompt-card"
                         x-data="{ show: false, flipped: false, copied: false, toast: false }" 
                         x-init="setTimeout(() => show = true, {{ $index * 80 }})" 
                         :class="{ 'visible': show }">

                        <!-- Flip Container -->
                        <div class="flip-container rounded-2xl shadow-lg shadow-black/30 hover:shadow-2xl hover:shadow-pink-500/20 transition-shadow duration-300 cursor-pointer"
                             :class="{ 'flipped': flipped }"
                             @click="flipped = !flipped">

                            <div class="flip-card">
                                
                                <!-- ===== FRONT FACE ===== -->
                                <div class="flip-front bg-slate-800 border border-white/10">
                                    <!-- Full portrait image / Slideshow -->
                                    @php $images = $prompt->images ?? []; $imgCount = count($images); @endphp
                                    @if($imgCount > 0)
                                        @if($imgCount == 1)
                                            <img src="{{ $images[0] }}" alt="{{ $prompt->title }}" class="w-full h-full object-cover {{ !$hasAccess ? 'brightness-50' : '' }}">
                                        @else
                                            <div class="w-full h-full relative" x-data="{ currentSlide: 0, total: {{ $imgCount }}, timer: null }" 
                                                 x-init="timer = setInterval(() => { currentSlide = (currentSlide + 1) % total }, 3000)"
                                                 @mouseenter="clearInterval(timer)"
                                                 @mouseleave="timer = setInterval(() => { currentSlide = (currentSlide + 1) % total }, 3000)">
                                                @foreach($images as $imgIdx => $img)
                                                    <img src="{{ $img }}" alt="{{ $prompt->title }}" 
                                                         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out {{ !$hasAccess ? 'brightness-50' : '' }}"
                                                         :class="currentSlide === {{ $imgIdx }} ? 'opacity-100 z-10' : 'opacity-0 z-0'" />
                                                @endforeach
                                                <!-- Slide indicators -->
                                                <div class="absolute top-2 inset-x-0 flex justify-center gap-1.5 z-20">
                                                    @foreach($images as $imgIdx => $img)
                                                        <div class="w-1.5 h-1.5 rounded-full shadow-sm transition-all duration-300"
                                                             :class="currentSlide === {{ $imgIdx }} ? 'bg-white scale-125' : 'bg-white/40'"></div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-indigo-600/30 via-purple-700/30 to-pink-600/30 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white/15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <!-- Gradient overlay at bottom -->
                                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/90 via-black/50 to-transparent z-30"></div>

                                    <!-- Title & Badge at bottom -->
                                    <div class="absolute inset-x-0 bottom-0 p-4 z-40">
                                        <h3 class="text-base font-bold text-white leading-tight mb-1 drop-shadow-lg">{{ $prompt->title }}</h3>
                                        @if($prompt->description)
                                            <p class="text-xs text-gray-300/80 line-clamp-1 drop-shadow">{{ $prompt->description }}</p>
                                        @endif
                                        @php $catInfo = $prompt->getCategoryInfo(); @endphp
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-white/70 bg-white/10 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                                {{ $catInfo['icon'] }} {{ $catInfo['label'] }}
                                            </span>
                                            <span class="inline-flex items-center text-[10px] text-yellow-400">
                                                @for($i = 0; $i < ($prompt->rating ?? 3); $i++)★@endfor
                                            </span>
                                        </div>
                                        @if($prompt->tags)
                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                @foreach(array_slice($prompt->getTagsArray(), 0, 3) as $tag)
                                                    <span class="text-[9px] text-pink-300/70 bg-pink-500/10 px-1.5 py-0.5 rounded">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Tier Badge -->
                                    @if($isPremium)
                                        <div class="absolute top-3 right-3 z-10 bg-gradient-to-r from-yellow-400 to-yellow-600 text-black text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            Premium
                                        </div>
                                    @else
                                        <div class="absolute top-3 right-3 z-10 bg-green-500/80 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                                            Free
                                        </div>
                                    @endif

                                    <!-- Flip hint -->
                                    <div class="absolute top-3 left-3 z-10 bg-black/50 backdrop-blur-sm text-white/70 text-[10px] font-medium px-2.5 py-1 rounded-full flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        <span class="hidden sm:inline">Hover</span>
                                        <span class="sm:hidden">Tekan</span>
                                    </div>
                                </div>

                                <!-- ===== BACK FACE ===== -->
                                <div class="flip-back border border-white/10" style="background: rgba(15, 23, 42, 0.95);">
                                    <div class="w-full h-full flex flex-col p-5">
                                        <!-- Header -->
                                        <div class="mb-3 shrink-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-sm font-bold text-white truncate pr-2">{{ $prompt->title }}</h4>
                                                @if($isPremium)
                                                    <span class="shrink-0 bg-yellow-500/20 text-yellow-400 text-[10px] font-bold px-2 py-0.5 rounded-full">⭐ Premium</span>
                                                @else
                                                    <span class="shrink-0 bg-green-500/20 text-green-400 text-[10px] font-bold px-2 py-0.5 rounded-full">Free</span>
                                                @endif
                                            </div>
                                            <div class="h-px bg-white/10"></div>
                                        </div>

                                        <!-- Prompt content area -->
                                        <div class="flex-grow overflow-y-auto min-h-0 mb-4 pr-1" style="scrollbar-width: thin; scrollbar-color: #475569 transparent;">
                                            @if(!$hasAccess)
                                                <div class="h-full flex flex-col items-center justify-center text-center px-4">
                                                    <div class="w-14 h-14 rounded-2xl bg-yellow-500/10 flex items-center justify-center mb-4">
                                                        <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    </div>
                                                    <p class="text-sm font-bold text-white mb-1">Prompt Terkunci 🔒</p>
                                                    @auth
                                                        {{-- Logged in but free tier --}}
                                                        <p class="text-xs text-gray-400 mb-5 leading-relaxed">Naik taraf ke <span class="text-yellow-400 font-semibold">Premium</span> untuk buka kunci prompt ini dan semua prompt eksklusif.</p>
                                                        <a href="{{ route('pricing.index') }}" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black text-xs font-bold px-5 py-2.5 rounded-full hover:opacity-90 transition shadow-lg shadow-yellow-500/30 flex items-center gap-1.5 mx-auto" @click.stop>
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                            Naik Taraf Premium
                                                        </a>
                                                    @else
                                                        {{-- Guest / not logged in --}}
                                                        <p class="text-xs text-gray-400 mb-5 leading-relaxed">Sila <span class="text-pink-400 font-semibold">log masuk</span> untuk melihat prompt premium ini.</p>
                                                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold px-5 py-2.5 rounded-full hover:opacity-90 transition shadow-lg shadow-purple-500/30 flex items-center gap-1.5 mx-auto mb-2" @click.stop>
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                                            Log Masuk
                                                        </a>
                                                        <a href="{{ route('register') }}" class="text-[10px] text-gray-500 hover:text-gray-300 transition" @click.stop>
                                                            Belum ada akaun? <span class="underline">Daftar</span>
                                                        </a>
                                                    @endauth
                                                </div>
                                            @else
                                                <div class="text-gray-300 text-xs font-mono leading-relaxed break-words select-all whitespace-pre-wrap">{{ $prompt->prompt_text }}</div>
                                            @endif
                                        </div>

                                        <!-- Footer actions -->
                                        @if($hasAccess)
                                        <div class="shrink-0 relative">
                                            <button @click.stop="navigator.clipboard.writeText(`{{ addslashes($prompt->prompt_text) }}`); copied = true; toast = true; setTimeout(() => { copied = false; toast = false; }, 2000)"
                                                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold transition border border-white/10"
                                                :class="copied ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-white/10 text-white hover:bg-white/20'">
                                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                <svg x-cloak x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span x-show="!copied">Salin Prompt</span>
                                                <span x-cloak x-show="copied">Disalin!</span>
                                            </button>
                                        </div>
                                        @endif

                                        <!-- Flip back hint -->
                                        <p class="text-center text-[10px] text-gray-600 mt-2">
                                            <span class="hidden sm:inline">Hover keluar untuk terbalik</span>
                                            <span class="sm:hidden">Tekan untuk terbalik</span>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($prompts->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="glass-card rounded-2xl px-6 py-3 inline-flex items-center gap-2">
                    @if($prompts->onFirstPage())
                        <span class="px-3 py-2 text-gray-600 text-sm cursor-not-allowed">← Sebelum</span>
                    @else
                        <a href="{{ $prompts->previousPageUrl() }}" class="px-3 py-2 text-gray-300 hover:text-white text-sm transition rounded-lg hover:bg-white/10">← Sebelum</a>
                    @endif

                    <span class="text-sm text-gray-400">Halaman {{ $prompts->currentPage() }} / {{ $prompts->lastPage() }}</span>

                    @if($prompts->hasMorePages())
                        <a href="{{ $prompts->nextPageUrl() }}" class="px-3 py-2 text-gray-300 hover:text-white text-sm transition rounded-lg hover:bg-white/10">Seterusnya →</a>
                    @else
                        <span class="px-3 py-2 text-gray-600 text-sm cursor-not-allowed">Seterusnya →</span>
                    @endif
                </div>
            </div>
            @endif
            
        </div>

        <!-- Footer -->
        <footer class="border-t border-white/5 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Brand -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-7 h-7 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                            <span class="text-lg font-bold tracking-tight text-white">Prompt<span class="text-pink-500">Lib</span></span>
                        </div>
                        <p class="text-sm text-gray-500 leading-relaxed">Perpustakaan prompt AI untuk menjana imej berkualiti tinggi. Dikurasi khas untuk kreativiti tanpa batas.</p>
                    </div>
                    <!-- Links -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-4">Pautan Pantas</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-white transition">Laman Utama</a></li>
                            <li><a href="{{ route('home', ['filter' => 'free']) }}" class="text-sm text-gray-500 hover:text-white transition">Prompt Percuma</a></li>
                            <li><a href="{{ route('home', ['filter' => 'premium']) }}" class="text-sm text-gray-500 hover:text-white transition">Prompt Premium</a></li>
                        </ul>
                    </div>
                    <!-- Account -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-4">Akaun</h4>
                        <ul class="space-y-2">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-white transition">Dashboard</a></li>
                                <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-500 hover:text-white transition">Tetapan Profil</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-white transition">Log Masuk</a></li>
                                <li><a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-white transition">Daftar Akaun Baru</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
                <div class="border-t border-white/5 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                    <p class="text-xs text-gray-600">&copy; {{ date('Y') }} PromptLib. made by rafiridzuan (intern khtp)</p>
                    <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs text-gray-400">
                        <span class="flex items-center gap-1.5 text-purple-400 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Pelawat: <strong class="text-white font-mono">{{ number_format($totalVisitors) }}</strong>
                        </span>
                        <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                        <span class="text-gray-400">
                            IP Unik: <strong class="text-white font-mono">{{ number_format($uniqueVisitors) }}</strong>
                        </span>
                    </div>
                </div>

                <!-- Last Updated Date & Time Display (Bottom Center) -->
                <div class="mt-4 pt-4 border-t border-white/5 text-center">
                    <p class="text-xs text-gray-400 font-mono inline-flex items-center justify-center gap-1.5 px-4 py-1.5 rounded-full bg-white/5 border border-white/10">
                        <span class="text-purple-400 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Masa & Tarikh Kemaskini Terakhir:
                        </span>
                        <strong class="text-white font-mono">{{ now()->format('d/m/Y h:i:s A') }}</strong>
                    </p>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <div x-data="{ showTop: false }" 
             x-init="window.addEventListener('scroll', () => showTop = window.scrollY > 500)"
             class="fixed bottom-6 right-6 z-50">
            <button x-cloak x-show="showTop" 
                    x-transition:enter="transition ease-out duration-300" 
                    x-transition:enter-start="opacity-0 translate-y-4" 
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    @click="window.scrollTo({ top: 0, behavior: 'smooth' })" 
                    class="p-3 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg shadow-purple-500/30 hover:opacity-90 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
            </button>
        </div>

        <!-- Ctrl+K Search Shortcut + AJAX Filter -->
        <script>
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.getElementById('globalSearch');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });

            // AJAX filtering - no page reload, no scroll to top
            function showSkeletonGrid() {
                const container = document.getElementById('prompt-results');
                if (!container) return;
                let skeletons = '';
                for (let i = 0; i < 8; i++) {
                    skeletons += `
                        <div class="prompt-card visible">
                            <div class="flip-container rounded-2xl shadow-lg shadow-black/30">
                                <div class="skeleton w-full h-full" style="aspect-ratio:9/16;"></div>
                            </div>
                        </div>`;
                }
                container.innerHTML = `
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        ${skeletons}
                    </div>`;
            }

            async function ajaxFilter(url) {
                showSkeletonGrid();
                try {
                    const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await resp.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Replace grid
                    const newResults = doc.getElementById('prompt-results');
                    const container = document.getElementById('prompt-results');
                    if (newResults && container) {
                        container.innerHTML = newResults.innerHTML;
                    }

                    // Replace filter bar + category pills (hero section updates)
                    const newHero = doc.querySelector('.relative.z-20');
                    const oldHero = document.querySelector('.relative.z-20');
                    if (newHero && oldHero) {
                        oldHero.outerHTML = newHero.outerHTML;
                    }

                    // Replace category pills
                    const newCats = doc.querySelector('.animate-fade-in-up.relative.z-10');
                    const oldCats = document.querySelector('.animate-fade-in-up.relative.z-10');
                    if (newCats && oldCats) {
                        oldCats.outerHTML = newCats.outerHTML;
                    }

                    // Re-init Alpine for new DOM elements
                    if (window.Alpine) {
                        document.querySelectorAll('#prompt-results [x-data]').forEach(el => {
                            if (!el._x_dataStack) Alpine.initTree(el);
                        });
                        document.querySelectorAll('.relative.z-20 [x-data]').forEach(el => {
                            if (!el._x_dataStack) Alpine.initTree(el);
                        });
                    }

                    // Re-bind filter links
                    bindFilterLinks();

                    // Update URL
                    history.pushState(null, '', url);

                    // Animate cards in
                    document.querySelectorAll('#prompt-results .prompt-card').forEach((card, i) => {
                        setTimeout(() => card.classList.add('visible'), i * 60);
                    });
                } catch (err) {
                    console.error('Filter error:', err);
                    window.location.href = url; // fallback
                }
            }

            function bindFilterLinks() {
                document.querySelectorAll('a.filter-link').forEach(link => {
                    link.removeEventListener('click', handleFilterClick);
                    link.addEventListener('click', handleFilterClick);
                });
            }

            function handleFilterClick(e) {
                e.preventDefault();
                const url = e.currentTarget.getAttribute('href');
                if (url) ajaxFilter(url);
            }

            // Handle browser back/forward
            window.addEventListener('popstate', function() {
                ajaxFilter(window.location.href);
            });

            // Initial bind
            document.addEventListener('DOMContentLoaded', bindFilterLinks);
        </script>

    </body>
</html>
