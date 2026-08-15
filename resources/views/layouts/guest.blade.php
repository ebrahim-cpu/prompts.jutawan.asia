<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PromptLib') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            body { font-family: 'Inter', sans-serif; }
            .glass-card {
                background: rgba(30, 41, 59, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .text-gradient {
                background: linear-gradient(to right, #a855f7, #ec4899);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .swal2-popup { border-radius: 1.25rem !important; }
        </style>
    </head>
    <body class="antialiased" style="background-color: #0f172a;">
        
        <!-- Background Orbs -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <!-- Logo -->
            <div class="mb-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <svg class="w-10 h-10 text-pink-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                    <span class="text-2xl font-bold tracking-tight text-white">Prompt<span class="text-pink-500">Lib</span></span>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md mt-4 px-8 py-8 glass-card overflow-hidden sm:rounded-2xl shadow-2xl shadow-purple-500/10">
                {{ $slot }}
            </div>

            <!-- Back to home link -->
            <a href="{{ route('home') }}" class="mt-6 text-sm text-gray-400 hover:text-white transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Laman Utama
            </a>

            <!-- Footer: Last Date & Time Updated (Bottom Center) -->
            <footer class="py-4 text-center relative z-10 mt-6">
                <p class="text-xs text-gray-400 font-mono flex items-center justify-center gap-1.5 flex-wrap">
                    <span class="text-purple-400 font-semibold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Masa & Tarikh Kemaskini Terakhir:
                    </span>
                    <strong class="text-white font-mono bg-white/5 border border-white/10 px-2.5 py-0.5 rounded-md">{{ now()->format('d/m/Y h:i:s A') }}</strong>
                </p>
            </footer>
        </div>

        <!-- Global SweetAlert2 Handler Script -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                @if (session('success'))
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
                        title: 'Ralat!',
                        text: "{{ $errors->first() }}",
                        confirmButtonColor: '#ef4444',
                        background: '#1e293b',
                        color: '#ffffff'
                    });
                @endif
            });
        </script>
    </body>
</html>
