<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white">
            Tetapan Profil Saya 👤
        </h2>
        <p class="text-sm text-gray-400 mt-1">Urus maklumat akaun, gambar profil dan keselamatan kata laluan anda.</p>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Admin Only: Log Pelawat Quick Card -->
            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="p-5 sm:p-6 bg-gradient-to-r from-amber-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-sm rounded-2xl border border-amber-500/30 shadow-xl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 flex items-center justify-center border border-amber-500/30 text-amber-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2 flex-wrap">
                                    Log Pelawat & Analitik Laman 👁️
                                    <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Akses Admin</span>
                                </h3>
                                <p class="text-xs text-gray-300 mt-0.5">Lihat senarai pelawat, alamat IP, peranti, serta tarikh & masa lawatan ke laman web ini.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.visitors.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-amber-500 to-purple-600 text-white font-bold text-sm rounded-xl hover:opacity-90 transition shadow-lg shadow-amber-500/20 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Buka Log Pelawat →
                        </a>
                    </div>
                </div>
            @endif

            <div class="p-5 sm:p-8 bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 shadow-xl">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-5 sm:p-8 bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 shadow-xl">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-5 sm:p-8 bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 shadow-xl">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
