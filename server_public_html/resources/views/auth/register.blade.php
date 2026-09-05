<x-guest-layout>
    <div class="text-center space-y-5">
        <!-- Logo / Icon -->
        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-600 flex items-center justify-center shadow-lg shadow-purple-500/30 ring-4 ring-white/10">
            <span class="text-3xl">✨</span>
        </div>

        <div>
            <h2 class="text-2xl font-extrabold text-white tracking-tight">Pendaftaran Akaun PromptLib</h2>
            <p class="text-gray-400 text-sm mt-1.5 max-w-sm mx-auto">
                Akses dan pendaftaran sistem ini adalah khusus menggunakan <strong class="text-white">Akaun Google</strong> sahaja.
            </p>
        </div>

        <!-- Google Registration Button -->
        <div class="pt-2">
            <a href="{{ route('auth.google') }}" 
               class="w-full flex items-center justify-center gap-3 py-3.5 px-6 rounded-2xl bg-white hover:bg-gray-100 text-gray-900 font-extrabold text-sm transition shadow-xl hover:scale-[1.02] cursor-pointer">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Daftar / Log Masuk dengan Google</span>
            </a>
        </div>

        <div class="bg-gray-900/60 border border-white/10 rounded-xl p-4 text-xs text-gray-400 leading-relaxed text-left">
            <p class="font-bold text-gray-300 mb-1 flex items-center gap-1.5">
                <span>🔒 Akses Pantas 1-Klik</span>
            </p>
            <p>Pendaftaran adalah serta-merta menggunakan Google OAuth tanpa perlu mengisi borang atau mengingati kata laluan berasingan.</p>
        </div>

        <p class="pt-2 text-center text-xs text-gray-500">
            Sudah mendaftar sebelum ini? 
            <a href="{{ route('auth.google') }}" class="text-pink-400 hover:text-pink-300 font-semibold transition">Log Masuk dengan Google</a>
        </p>
    </div>
</x-guest-layout>
