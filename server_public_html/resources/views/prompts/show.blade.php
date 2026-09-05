<!DOCTYPE html>
<html lang="ms" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $prompt->title }} — PromptLib</title>
        <meta name="description" content="{{ $prompt->description ?? 'Lihat prompt AI berkualiti tinggi dan foto berkaitan di PromptLib.' }}">
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
            [x-cloak] { display: none !important; }
            ::-webkit-scrollbar { width: 8px; }
            ::-webkit-scrollbar-track { background: #0f172a; }
            ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #475569; }
        </style>
    </head>
    <body class="antialiased relative selection:bg-primary selection:text-white">
        
        <!-- Background Orbs -->
        <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-pink-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-indigo-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        </div>

        <!-- Navigation -->
        <nav class="glass-card fixed w-full z-[110] transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white text-xs font-semibold border border-white/10 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            <span>Kembali</span>
                        </a>
                        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                            <svg class="w-7 h-7 text-pink-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                            <span class="text-lg font-bold tracking-tight text-white hidden sm:inline">Prompt<span class="text-pink-500">Lib</span></span>
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-xs font-semibold text-gray-300 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition">📊 Dashboard</a>
                            @else
                                <a href="{{ route('auth.google') }}" class="text-xs font-semibold bg-gradient-to-r from-purple-600 to-pink-600 px-4 py-2 rounded-lg text-white shadow-lg transition hover:opacity-90">Log Masuk</a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Container -->
        <main class="pt-24 pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-12">

            @php
                $isUpcoming = $prompt->is_upcoming;
                $isPremium = $prompt->is_premium;
                $hasAccess = true;
                if ($isPremium && !$isUpcoming) {
                    if (!auth()->check() || (!auth()->user()->isPremiumActive() && auth()->user()->role !== 'admin')) {
                        $hasAccess = false;
                    }
                }
                $images = array_values(array_filter($prompt->images ?? []));
                $catInfo = $prompt->getCategoryInfo();
            @endphp

            <!-- Prompt Header & Showcase Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left Column: Image Showcase / Gallery (5 cols) -->
                <div class="lg:col-span-5 space-y-4" x-data="{ selectedImg: '{{ $images[0] ?? '' }}', modalOpen: false }">
                    <div class="relative rounded-3xl overflow-hidden glass-card border border-white/10 aspect-[3/4] shadow-2xl group">
                        @if(!empty($images))
                            <img :src="selectedImg || '{{ $images[0] }}'" alt="{{ $prompt->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 {{ (!$hasAccess && !$isUpcoming) ? 'brightness-50' : '' }}">
                            <button @click="modalOpen = true" class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-md text-white p-2.5 rounded-xl border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity" title="Lihat saiz penuh">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                            </button>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-600/30 via-purple-700/30 to-pink-600/30 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif

                        <!-- Badge Overlay -->
                        <div class="absolute top-4 left-4 z-20 flex flex-wrap gap-2">
                            @if($isUpcoming)
                                <span class="bg-cyan-500/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">⏳ Upcoming</span>
                            @elseif($isPremium)
                                <span class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-black text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">⭐ Premium</span>
                            @else
                                <span class="bg-green-500/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">Free</span>
                            @endif

                            @if($prompt->is_featured)
                                <span class="bg-pink-500/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">🔥 Featured</span>
                            @endif
                        </div>
                    </div>

                    <!-- Thumbnails List (If multiple) -->
                    @if(count($images) > 1)
                        <div class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
                            @foreach($images as $idx => $img)
                                <button type="button" @click="selectedImg = '{{ $img }}'" 
                                        class="w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 transition-all cursor-pointer"
                                        :class="selectedImg === '{{ $img }}' ? 'border-pink-500 ring-2 ring-pink-500/50 scale-105' : 'border-white/10 opacity-70 hover:opacity-100'">
                                    <img src="{{ $img }}" alt="{{ $prompt->title }} thumbnail {{ $idx + 1 }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Lightbox Modal -->
                    <template x-teleport="body">
                        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/90 backdrop-blur-lg" @click.self="modalOpen = false">
                            <button @click="modalOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white p-2 rounded-full bg-white/10 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <img :src="selectedImg || '{{ $images[0] ?? '' }}'" alt="{{ $prompt->title }}" class="max-w-full max-h-[90vh] object-contain rounded-2xl shadow-2xl">
                        </div>
                    </template>
                </div>

                <!-- Right Column: Prompt Details & Text Box (7 cols) -->
                <div class="lg:col-span-7 space-y-6" x-data="{ copied: false }">
                    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                        
                        <!-- Header info -->
                        <div>
                            <div class="flex items-center gap-2 mb-3 flex-wrap">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-white/80 bg-white/10 px-3 py-1 rounded-full">
                                    {{ $catInfo['icon'] }} {{ $catInfo['label'] }}
                                </span>
                                <span class="text-yellow-400 text-sm font-bold flex items-center gap-1">
                                    @for($i = 0; $i < ($prompt->rating ?? 3); $i++)★@endfor
                                    <span class="text-xs text-gray-400 ml-1">({{ $prompt->rating ?? 3 }}/5)</span>
                                </span>
                            </div>

                            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight mb-3">{{ $prompt->title }}</h1>

                            @if($prompt->description)
                                <p class="text-base text-gray-300 leading-relaxed">{{ $prompt->description }}</p>
                            @endif

                            @if($prompt->tags)
                                <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-white/5">
                                    @foreach($prompt->getTagsArray() as $tag)
                                        <a href="{{ route('home', ['tag' => $tag]) }}" class="text-xs text-pink-300 bg-pink-500/10 hover:bg-pink-500/20 px-2.5 py-1 rounded-lg transition border border-pink-500/20">#{{ $tag }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Prompt Text Section -->
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                                    <span>Teks Prompt AI</span>
                                </h3>
                                @if($hasAccess && !$isUpcoming)
                                    <button type="button" @click="navigator.clipboard.writeText(`{{ addslashes($prompt->prompt_text) }}`); copied = true; setTimeout(() => copied = false, 2500)"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition border border-pink-500/30 bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:opacity-90 shadow-lg shadow-purple-500/20 cursor-pointer">
                                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <svg x-cloak x-show="copied" class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="copied ? 'Berjaya Disalin!' : 'Salin Prompt'"></span>
                                    </button>
                                @endif
                            </div>

                            <!-- Content Display / Access Check -->
                            <div class="bg-gray-950/80 border border-white/10 rounded-2xl p-5 min-h-[140px] flex items-center justify-center">
                                @if($isUpcoming)
                                    <div class="text-center py-6 px-4">
                                        <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center mx-auto mb-3">
                                            <span class="text-3xl">⏳</span>
                                        </div>
                                        <h4 class="text-base font-bold text-cyan-300 mb-1">Akan Datang / Upcoming</h4>
                                        <p class="text-xs text-gray-400 max-w-md mx-auto leading-relaxed">Prompt ini masih dalam peringkat draf dan belum diterbitkan secara rasmi. Sila nantikan kemas kini seterusnya!</p>
                                    </div>
                                @elseif(!$hasAccess)
                                    <div class="text-center py-6 px-4 space-y-4">
                                        <div class="w-16 h-16 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center mx-auto">
                                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-white mb-1">Prompt Premium Terkunci 🔒</h4>
                                            <p class="text-xs text-gray-400 max-w-md mx-auto leading-relaxed">Akses teks prompt penuh ini adalah eksklusif untuk ahli Premium. Sila log masuk atau naik taraf akaun anda.</p>
                                        </div>
                                        <div class="flex flex-wrap justify-center gap-3 pt-2">
                                            @auth
                                                <a href="{{ route('pricing.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 text-black text-xs font-extrabold shadow-lg shadow-yellow-500/20 hover:opacity-90 transition">
                                                    ⭐ Naik Taraf Ke Premium
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold shadow-lg hover:opacity-90 transition">
                                                    🔑 Log Masuk
                                                </a>
                                                <a href="{{ route('register') }}" class="px-4 py-2.5 rounded-xl bg-white/10 text-gray-300 hover:text-white text-xs font-medium transition">
                                                    Daftar Akaun
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full text-gray-200 font-mono text-sm leading-relaxed whitespace-pre-wrap select-all break-words">{{ $prompt->prompt_text }}</div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Related Prompts Section -->
            @if($relatedPrompts->count() > 0)
                <div class="pt-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div>
                            <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                                <span>🎨 Prompt Berkaitan</span>
                            </h2>
                            <p class="text-xs text-gray-400 mt-1">Prompt lain dalam kategori <span class="text-pink-400 font-semibold">{{ $catInfo['label'] }}</span> atau tag berkaitan.</p>
                        </div>
                        <a href="{{ route('home', ['category' => $prompt->category]) }}" class="text-xs text-pink-400 hover:text-pink-300 font-semibold transition flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        @foreach($relatedPrompts as $relPrompt)
                            @php
                                $relImg = $relPrompt->getFirstImageUrl();
                                $relCat = $relPrompt->getCategoryInfo();
                            @endphp
                            <a href="{{ route('prompts.show', $relPrompt->id) }}" class="group block glass-card rounded-2xl overflow-hidden border border-white/10 hover:border-pink-500/40 hover:shadow-xl hover:shadow-pink-500/10 transition-all duration-300">
                                <div class="relative aspect-[3/4] overflow-hidden bg-slate-800">
                                    @if($relImg)
                                        <img src="{{ $relImg }}" alt="{{ $relPrompt->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-indigo-600/30 via-purple-700/30 to-pink-600/30 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <!-- Tier Badge -->
                                    @if($relPrompt->is_upcoming)
                                        <span class="absolute top-3 right-3 bg-cyan-500/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Upcoming</span>
                                    @elseif($relPrompt->is_premium)
                                        <span class="absolute top-3 right-3 bg-gradient-to-r from-yellow-400 to-yellow-600 text-black text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Premium</span>
                                    @else
                                        <span class="absolute top-3 right-3 bg-green-500/80 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Free</span>
                                    @endif

                                    <!-- Gradient Overlay -->
                                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                                    <!-- Bottom Info -->
                                    <div class="absolute inset-x-0 bottom-0 p-3 z-10">
                                        <h4 class="text-sm font-bold text-white truncate group-hover:text-pink-300 transition-colors">{{ $relPrompt->title }}</h4>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="text-[9px] text-white/70 bg-white/10 px-2 py-0.5 rounded-full">{{ $relCat['icon'] }} {{ $relCat['label'] }}</span>
                                            <span class="text-[9px] text-yellow-400 ml-auto">@for($i = 0; $i < ($relPrompt->rating ?? 3); $i++)★@endfor</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </main>

        <!-- Footer -->
        <footer class="border-t border-white/10 bg-slate-950/80 py-8 text-center text-xs text-gray-500">
            <p>Hak Cipta Terpelihara &copy; {{ date('Y') }} PromptLib — Koleksi Prompt AI Terbaik.</p>
        </footer>
    </body>
</html>
