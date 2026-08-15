<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-red-500/30">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white mb-1">Google Authentication Gagal! ✕</h2>
        <p class="text-xs text-red-400 font-mono">STATUS: ERROR | TEMPORARY TROUBLESHOOT PAGE</p>
    </div>

    <div class="bg-white/5 rounded-2xl p-6 border border-red-500/30 space-y-4 text-left">
        <div>
            <h3 class="text-xs uppercase tracking-wider text-red-400 font-bold mb-1">Mesej Ralat (Error Message):</h3>
            <div class="bg-black/40 p-4 rounded-xl border border-red-500/20 text-red-300 font-mono text-xs break-all leading-relaxed">
                {{ session('error_message') ?? request('error') ?? 'Ralat tidak diketahui semasa pengesahan Google.' }}
            </div>
        </div>

        <div class="bg-black/30 p-3 rounded-xl text-xs font-mono text-gray-400 space-y-1">
            <div><span class="text-gray-500">HTTP Code:</span> 500 / Error</div>
            <div><span class="text-gray-500">Session ID:</span> {{ session()->getId() }}</div>
            <div><span class="text-gray-500">Timestamp:</span> {{ now() }}</div>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-2">
        <a href="{{ route('auth.google') }}" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white font-bold py-3 rounded-xl text-center text-sm uppercase tracking-wider hover:opacity-90 transition shadow-lg shadow-red-500/30">
            ↺ Cuba Log Masuk Google Sekali Lagi
        </a>
        <a href="{{ route('login') }}" class="w-full bg-white/10 text-white font-semibold py-2.5 rounded-xl text-center text-xs hover:bg-white/20 transition">
            Kembali ke Halaman Log Masuk
        </a>
    </div>
</x-guest-layout>
