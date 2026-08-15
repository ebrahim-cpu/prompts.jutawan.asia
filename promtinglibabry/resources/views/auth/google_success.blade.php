<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-green-500/30">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white mb-1">Google Authentication Berjaya! 🎉</h2>
        <p class="text-xs text-green-400 font-mono">STATUS: 200 OK | TEMPORARY TROUBLESHOOT PAGE</p>
    </div>

    <div class="bg-white/5 rounded-2xl p-6 border border-white/10 space-y-4 text-left">
        <div class="flex items-center gap-4 pb-4 border-b border-white/10">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-14 h-14 rounded-full object-cover border-2 border-purple-500">
            @else
                <div class="w-14 h-14 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold text-xl flex items-center justify-center">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h3 class="text-lg font-bold text-white">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-300">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                <span class="text-gray-400 block mb-0.5">ID Rekod User:</span>
                <span class="text-pink-400 font-mono font-bold">#{{ Auth::user()->id }}</span>
            </div>
            <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                <span class="text-gray-400 block mb-0.5">Google ID:</span>
                <span class="text-purple-400 font-mono font-bold truncate block">{{ Auth::user()->google_id ?? 'N/A' }}</span>
            </div>
            <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                <span class="text-gray-400 block mb-0.5">Peranan (Role):</span>
                <span class="text-yellow-400 font-bold uppercase">{{ Auth::user()->role }}</span>
            </div>
            <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                <span class="text-gray-400 block mb-0.5">Pakej (Tier):</span>
                <span class="text-green-400 font-bold uppercase">{{ Auth::user()->tier }}</span>
            </div>
        </div>

        <div class="bg-black/30 p-3 rounded-xl text-xs font-mono text-gray-300 space-y-1">
            <div><span class="text-gray-500">Auth Check:</span> {{ Auth::check() ? 'TRUE (Logged In)' : 'FALSE' }}</div>
            <div><span class="text-gray-500">Session ID:</span> {{ session()->getId() }}</div>
            <div><span class="text-gray-500">Verified At:</span> {{ Auth::user()->email_verified_at }}</div>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-2">
        <a href="{{ route('dashboard') }}" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 rounded-xl text-center text-sm uppercase tracking-wider hover:opacity-90 transition">
            Teruskan ke Dashboard →
        </a>
        <a href="{{ route('profile.edit') }}" class="w-full bg-white/10 text-white font-semibold py-2.5 rounded-xl text-center text-xs hover:bg-white/20 transition">
            Lihat Profil User
        </a>
    </div>
</x-guest-layout>
