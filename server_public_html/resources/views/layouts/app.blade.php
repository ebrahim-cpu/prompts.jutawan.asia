<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PromptLib') }}</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎨</text></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            [x-cloak] { display: none !important; }
            .swal2-popup { border-radius: 1.25rem !important; }
        </style>
    </head>
    <body class="antialiased bg-[#0f172a]" style="min-height: 100vh;">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Global Expiry Warning -->
            @if(auth()->check() && auth()->user()->isSubscriptionExpiringSoon() && request()->route()->getName() !== 'dashboard')
                <div class="bg-gradient-to-r from-orange-500 to-yellow-500 text-black px-4 py-2" 
                     x-data="{ 
                        show: true,
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
                     }" x-init="countdown(); setInterval(() => countdown(), 1000);" x-show="show" x-transition>
                    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 relative">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-sm font-bold">
                                Langganan Premium anda akan tamat dalam <span x-text="days"></span>H <span x-text="hours"></span>J <span x-text="minutes"></span>M <span x-text="seconds"></span>S.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('pricing.index') }}" class="text-xs font-bold bg-black text-white px-3 py-1.5 rounded-lg hover:bg-gray-900 transition whitespace-nowrap shadow-sm">
                                Perbaharui Sekarang
                            </a>
                            <button @click="show = false" class="text-black/60 hover:text-black transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-900/50 backdrop-blur-sm border-b border-white/5">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Global Footer: Last Date & Time Updated (Bottom Center) -->
            <footer class="py-6 border-t border-white/5 bg-gray-900/40 text-center relative z-10 mt-12">
                <div class="max-w-7xl mx-auto px-4 flex flex-col items-center justify-center gap-1">
                    <p class="text-xs text-gray-400 font-mono flex items-center justify-center gap-2 flex-wrap">
                        <span class="text-purple-400 font-semibold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Masa & Tarikh Kemaskini Terakhir:
                        </span>
                        <strong class="text-white font-mono bg-white/5 border border-white/10 px-3 py-1 rounded-lg">
                            {{ isset($prompt) && isset($prompt->updated_at) ? $prompt->updated_at->format('d/m/Y h:i:s A') . ' (' . $prompt->updated_at->diffForHumans() . ')' : now()->format('d/m/Y h:i:s A') }}
                        </strong>
                    </p>
                    <p class="text-[11px] text-gray-600 mt-1">&copy; {{ date('Y') }} PromptLib — All Rights Reserved</p>
                </div>
            </footer>
        </div>

        <!-- Global SweetAlert2 Handler Script -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // 1. Profile & Save Success Alerts
                @if (session('status') === 'profile-updated')
                    Swal.fire({
                        icon: 'success',
                        title: 'Kemaskini Berjaya! 🎉',
                        text: 'Maklumat profil dan gambar profil anda telah berjaya disimpan.',
                        confirmButtonColor: '#9333ea',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @elseif (session('status') === 'password-updated')
                    Swal.fire({
                        icon: 'success',
                        title: 'Kata Laluan Dikemaskini! 🔒',
                        text: 'Kata laluan baru anda telah berjaya disimpan.',
                        confirmButtonColor: '#9333ea',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @elseif (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berjaya!',
                        text: "{{ session('success') }}",
                        confirmButtonColor: '#9333ea',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @elseif (session('status'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Notifikasi',
                        text: "{{ session('status') }}",
                        confirmButtonColor: '#9333ea',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @endif

                // 2. Error Alerts
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Ralat!',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#ef4444',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @endif

                @if ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Ralat Borang!',
                        text: "{{ $errors->first() }}",
                        confirmButtonColor: '#ef4444',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @endif
            });

            // Global Reusable Delete Confirmation SweetAlert2 Function
            function confirmDelete(form, message = 'Adakah anda pasti mahu memadamkan rekod ini?') {
                Swal.fire({
                    title: 'Pengesahan Padam ⚠️',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Padam!',
                    cancelButtonText: 'Batal',
                    background: '#1e293b',
                    color: '#ffffff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }
        </script>
    </body>
</html>
