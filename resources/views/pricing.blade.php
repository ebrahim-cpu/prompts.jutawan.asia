<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Naik Taraf Premium — {{ config('app.name', 'PromptLib') }}</title>
        <meta name="description" content="Pilih pelan Premium PromptLib dan buka kunci semua prompt AI berkualiti tinggi.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; min-height: 100vh; }
            .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 30px rgba(0,0,0,0.1); }
            .text-gradient { background: linear-gradient(to right, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased">

        <!-- Background Orbs -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        </div>

        <!-- Navigation -->
        <nav class="glass-card fixed w-full z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                        <span class="text-xl font-bold text-white">Prompt<span class="text-pink-500">Lib</span></span>
                    </a>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('home') }}" class="text-sm text-gray-300 hover:text-white transition px-3 py-2 rounded-lg hover:bg-white/10">← Kembali</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-300 hover:text-white transition px-3 py-2 rounded-lg hover:bg-white/10">Dashboard</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="relative z-10 pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-sm font-bold px-4 py-2 rounded-full mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    Naik Taraf Premium
                </div>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4">
                    Buka Kunci <span class="text-gradient">Semua Prompt</span>
                </h1>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                    Pilih pelan yang sesuai dengan keperluan anda. Akses penuh ke semua prompt AI premium berkualiti tinggi.
                </p>
            </div>

            @if(session('error'))
                <div class="mb-8 max-w-lg mx-auto bg-red-500/10 border border-red-500/30 text-red-400 px-5 py-4 rounded-xl text-sm text-center">
                    {{ session('error') }}
                </div>
            @endif

            @auth
                @if(auth()->user()->isPremiumActive())
                    <div class="mb-10 max-w-lg mx-auto glass-card rounded-2xl p-6 text-center border border-green-500/30">
                        <div class="text-3xl mb-2">🎉</div>
                        <h3 class="text-lg font-bold text-green-400 mb-2">Anda Sudah Premium!</h3>
                        <p class="text-sm text-gray-400">
                            @if(auth()->user()->premium_expires_at)
                                Langganan anda aktif sehingga <strong class="text-white">{{ auth()->user()->premium_expires_at->format('d M Y, h:i A') }}</strong>.
                                <br>Anda boleh melanjutkan langganan di bawah.
                            @else
                                Anda mempunyai akses <strong class="text-white">Seumur Hidup</strong>. Terima kasih atas sokongan anda! 💖
                            @endif
                        </p>
                    </div>
                @endif
            @endauth

            <!-- Plans Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($plans as $key => $plan)
                    <div class="relative group">
                        @if($plan['popular'])
                            <div class="absolute -top-3 inset-x-0 flex justify-center z-10">
                                <span class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-[10px] font-bold px-4 py-1 rounded-full shadow-lg uppercase tracking-wider">Paling Popular</span>
                            </div>
                        @endif
                        <div class="glass-card rounded-2xl p-6 h-full flex flex-col transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-purple-500/20 {{ $plan['popular'] ? 'border-purple-500/50 ring-1 ring-purple-500/30' : 'hover:border-white/20' }}">
                            <!-- Icon -->
                            <div class="text-4xl mb-4">{{ $plan['icon'] }}</div>

                            <!-- Plan Name -->
                            <h3 class="text-lg font-bold text-white mb-1">{{ $plan['name'] }}</h3>
                            <p class="text-xs text-gray-500 mb-4">{{ $plan['description'] }}</p>

                            <!-- Price -->
                            <div class="mb-6">
                                <span class="text-3xl font-extrabold text-white">{{ $plan['price_display'] }}</span>
                                @if($key !== 'lifetime')
                                    <span class="text-xs text-gray-500 ml-1">/ {{ strtolower($plan['name']) }}</span>
                                @else
                                    <span class="text-xs text-gray-500 ml-1">sekali bayar</span>
                                @endif
                            </div>

                            <!-- Features -->
                            <ul class="space-y-2 mb-6 flex-grow">
                                <li class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Semua prompt premium
                                </li>
                                <li class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Salin tanpa had
                                </li>
                                <li class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Prompt baru setiap minggu
                                </li>
                                @if($key === 'lifetime')
                                <li class="flex items-center gap-2 text-xs text-yellow-400 font-semibold">
                                    <svg class="w-4 h-4 text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Akses selama-lamanya!
                                </li>
                                @endif
                            </ul>

                            <!-- CTA Button -->
                            @auth
                                <form action="{{ route('pricing.checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $key }}">
                                    <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold transition {{ $plan['popular'] ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:opacity-90 shadow-lg shadow-purple-500/30' : 'bg-white/10 text-white hover:bg-white/20 border border-white/10' }}">
                                        Pilih Pelan Ini
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="w-full py-3 rounded-xl text-sm font-bold transition text-center block {{ $plan['popular'] ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:opacity-90 shadow-lg shadow-purple-500/30' : 'bg-white/10 text-white hover:bg-white/20 border border-white/10' }}">
                                    Log Masuk Dulu
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- FAQ -->
            <div class="mt-20 max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-white text-center mb-8">Soalan Lazim</h2>
                <div class="space-y-4">
                    <div class="glass-card rounded-xl p-5" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                            <span class="text-sm font-bold text-white">Apakah yang saya dapat dengan Premium?</span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="mt-3 text-sm text-gray-400 leading-relaxed">
                            Dengan aktif Premium, anda boleh melihat dan menyalin SEMUA prompt AI dalam koleksi kami termasuk prompt eksklusif yang tidak tersedia untuk pengguna percuma. Prompt baru ditambah setiap minggu!
                        </div>
                    </div>
                    <div class="glass-card rounded-xl p-5" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                            <span class="text-sm font-bold text-white">Bolehkah saya membatalkan langganan?</span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="mt-3 text-sm text-gray-400 leading-relaxed">
                            Ya! Semua pelan kami adalah bayaran sekali sahaja (bukan langganan berulang). Anda bayar dan nikmati akses penuh sehingga tempoh tamat — tiada caj tersembunyi.
                        </div>
                    </div>
                    <div class="glass-card rounded-xl p-5" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                            <span class="text-sm font-bold text-white">Apakah kaedah pembayaran yang diterima?</span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="mt-3 text-sm text-gray-400 leading-relaxed">
                            Kami menerima pembayaran melalui kad kredit/debit (Visa, Mastercard) melalui Stripe — platform pembayaran yang selamat dan dipercayai di seluruh dunia.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-white/5 relative z-10">
            <div class="max-w-7xl mx-auto px-4 py-8 text-center">
                <p class="text-xs text-gray-600">&copy; {{ date('Y') }} PromptLib. made by rafiridzuan (intern khtp)</p>
            </div>
        </footer>
    </body>
</html>
