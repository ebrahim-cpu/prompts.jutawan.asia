<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Dashboard</h2>
                <p class="text-sm text-gray-400 mt-1">Selamat datang kembali, {{ auth()->user()->name }}! 👋</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Lihat Koleksi Prompt
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-4 rounded-xl" role="alert" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            @endif

            <!-- Expiry Warning Banner -->
            @if(auth()->user()->isSubscriptionExpiringSoon())
                <div class="bg-gradient-to-r from-orange-500/10 to-yellow-500/10 border-2 border-orange-500/30 rounded-2xl p-5 mb-6 shadow-lg shadow-orange-500/5 relative overflow-hidden" 
                     x-data="{ 
                        showWarning: true,
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
                     }" x-init="countdown(); setInterval(() => countdown(), 1000);" x-show="showWarning" x-transition>
                    <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                        <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-5 relative z-10">
                        <!-- Countdown Timer -->
                        <div class="flex gap-2 shrink-0">
                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                <span class="text-xl font-extrabold text-orange-500" x-text="days"></span>
                                <span class="text-[9px] font-bold text-orange-400">HARI</span>
                            </div>
                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                <span class="text-xl font-extrabold text-orange-500" x-text="hours"></span>
                                <span class="text-[9px] font-bold text-orange-400">JAM</span>
                            </div>
                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                <span class="text-xl font-extrabold text-orange-500" x-text="minutes"></span>
                                <span class="text-[9px] font-bold text-orange-400">MINIT</span>
                            </div>
                            <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex flex-col items-center justify-center border border-orange-500/30">
                                <span class="text-xl font-extrabold text-orange-500" x-text="seconds"></span>
                                <span class="text-[9px] font-bold text-orange-400">SAAT</span>
                            </div>
                        </div>

                        <div class="text-center sm:text-left flex-grow">
                            <h3 class="text-lg font-bold text-white mb-1 flex items-center justify-center sm:justify-start gap-2">
                                ⚠️ Langganan Premium Hampir Tamat
                            </h3>
                            <p class="text-sm text-orange-200/80">Langganan anda tamat pada {{ auth()->user()->premium_expires_at->format('d M Y') }}. Perbaharui segera sebelum had masa ini!</p>
                        </div>
                        <div class="shrink-0 flex gap-3">
                            <a href="{{ route('pricing.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-yellow-500 text-black text-sm font-bold shadow-lg shadow-orange-500/20 hover:opacity-90 transition">
                                Perbaharui Sekarang
                            </a>
                            <button @click="showWarning = false" class="p-2.5 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition" title="Tutup peringatan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6 hover:border-white/20 transition">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-purple-500/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-white">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ auth()->user()->role === 'admin' ? 'bg-red-500/20 text-red-400' : 'bg-blue-500/20 text-blue-400' }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ auth()->user()->tier === 'premium' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-gray-700 text-gray-400' }}">
                            @if(auth()->user()->tier === 'premium') ⭐ Premium @else Free Tier @endif
                        </span>
                    </div>
                </div>

                <!-- Tier Status -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6 hover:border-white/20 transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl {{ auth()->user()->isPremiumActive() ? 'bg-yellow-500/20' : 'bg-gray-700' }} flex items-center justify-center">
                            @if(auth()->user()->isPremiumActive())
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @else
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            @endif
                        </div>
                        <h3 class="font-bold text-white">Status Akaun</h3>
                    </div>
                    @if(auth()->user()->isPremiumActive())
                        <p class="text-sm text-gray-300 mb-2">Anda mempunyai akses <strong class="text-white">penuh</strong> kepada semua koleksi prompt termasuk prompt Premium.</p>
                        @if(auth()->user()->premium_expires_at)
                            <p class="text-xs text-gray-500">Aktif sehingga: <strong class="text-gray-300">{{ auth()->user()->premium_expires_at->format('d M Y, h:i A') }}</strong></p>
                        @elseif(auth()->user()->role !== 'admin')
                            <p class="text-xs text-green-400 font-semibold">✅ Akses Seumur Hidup</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-300 mb-3">Anda hanya boleh melihat prompt <strong class="text-white">Free</strong> sahaja. Naik taraf untuk buka kunci semua!</p>
                        <a href="{{ route('pricing.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-black bg-gradient-to-r from-yellow-400 to-yellow-500 px-4 py-2 rounded-lg hover:opacity-90 transition shadow-sm">
                            ⭐ Naik Taraf Premium
                        </a>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6 hover:border-white/20 transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="font-bold text-white">Tindakan Pantas</h3>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 p-2.5 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition border border-white/5 hover:border-white/10">
                            <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Terokai Koleksi Prompt
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-2.5 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition border border-white/5 hover:border-white/10">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Tetapan Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Premium Upgrade Banner (for free users only) -->
            @if(!auth()->user()->isPremiumActive() && auth()->user()->role !== 'admin')
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-lg shadow-purple-500/20 overflow-hidden">
                <div class="p-8 flex flex-col sm:flex-row items-center gap-6">
                    <div class="shrink-0 w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <div class="flex-grow text-center sm:text-left">
                        <h3 class="text-xl font-bold text-white mb-1">Buka Kunci Semua Prompt Premium! ⭐</h3>
                        <p class="text-sm text-purple-100">Naik taraf akaun anda sekarang untuk akses penuh kepada ratusan prompt AI eksklusif. Bermula dari RM 3 sahaja.</p>
                    </div>
                    <a href="{{ route('pricing.index') }}" class="shrink-0 bg-white text-purple-700 font-bold text-sm px-6 py-3 rounded-xl hover:bg-purple-50 transition shadow-lg flex items-center gap-2">
                        ⭐ Lihat Pelan & Harga
                    </a>
                </div>
            </div>
            @endif

            <!-- Admin Section -->
            @if(auth()->user()->role === 'admin')
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden">
                <div class="p-6 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Panel Admin</h3>
                            <p class="text-sm text-gray-400">Urus prompt, pengguna dan sistem.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-4 p-4 rounded-xl border-2 border-dashed border-purple-500/40 hover:border-purple-400 hover:bg-purple-500/10 transition group shadow-lg shadow-purple-500/5">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition text-xl">
                            📊
                        </div>
                        <div>
                            <div class="font-bold text-white group-hover:text-purple-300 transition">Laporan Interactive</div>
                            <div class="text-xs text-purple-400 font-medium">Pelawat, Logins & Prompts</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.prompts.create') }}" class="flex items-center gap-4 p-4 rounded-xl border-2 border-dashed border-green-500/30 hover:border-green-400 hover:bg-green-500/5 transition group">
                        <div class="w-12 h-12 rounded-xl bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-white group-hover:text-green-400 transition">Tambah Prompt</div>
                            <div class="text-xs text-gray-500">Cipta prompt AI baharu</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.prompts.index') }}" class="flex items-center gap-4 p-4 rounded-xl border-2 border-dashed border-indigo-500/30 hover:border-indigo-400 hover:bg-indigo-500/5 transition group">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center group-hover:bg-indigo-500/30 transition">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-white group-hover:text-indigo-400 transition">Senarai Prompts</div>
                            <div class="text-xs text-gray-500">Edit & buang prompt</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-4 p-4 rounded-xl border-2 border-dashed border-pink-500/30 hover:border-pink-400 hover:bg-pink-500/5 transition group">
                        <div class="w-12 h-12 rounded-xl bg-pink-500/20 flex items-center justify-center group-hover:bg-pink-500/30 transition">
                            <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-white group-hover:text-pink-400 transition">Kategori & Tag</div>
                            <div class="text-xs text-gray-500">Urus kategori prompt</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-4 p-4 rounded-xl border-2 border-dashed border-cyan-500/30 hover:border-cyan-400 hover:bg-cyan-500/5 transition group">
                        <div class="w-12 h-12 rounded-xl bg-cyan-500/20 flex items-center justify-center group-hover:bg-cyan-500/30 transition">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <div class="font-bold text-white group-hover:text-cyan-400 transition">Urus Pengguna</div>
                            <div class="text-xs text-gray-500">Lihat, edit role & tier</div>
                        </div>
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
