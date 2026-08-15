<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">
                    {{ $isAdmin ? 'Log Sesi Log Masuk & Keluar (Semua Pengguna) 🔑' : 'Log Sesi Log Masuk & Keluar Saya 🔑' }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">
                    {{ $isAdmin ? 'Rekod jejak rasmi sesi log masuk dan log keluar pengguna ke aplikasi.' : 'Rekod rasmi aktiviti log masuk dan log keluar akaun anda.' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(($totalLogins + $totalLogouts) > 0)
                    <form action="{{ route('user_access_logs.clear') }}" method="POST" onsubmit="return confirmDelete(this, 'Adakah anda pasti mahu membersihkan rekod log masuk/keluar ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 text-sm font-medium rounded-xl border border-red-500/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Bersihkan Log
                        </button>
                    </form>
                @endif
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white text-sm font-medium rounded-xl hover:bg-white/20 transition border border-white/10">
                    ← Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Overview -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ number_format($totalLogins) }}</p>
                            <p class="text-xs text-gray-400">Log Masuk Berjaya</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ number_format($totalLogouts) }}</p>
                            <p class="text-xs text-gray-400">Log Keluar</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ number_format($todayAccesses) }}</p>
                            <p class="text-xs text-gray-400">Aktiviti Hari Ini</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457-.315-2.84-.877-4.085"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ number_format($uniqueIPs) }}</p>
                            <p class="text-xs text-gray-400">IP Unik</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar, Event Filter Tabs & Records Per Page Controls -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 space-y-4">
                
                <!-- Filter Tabs: Semua / Log Masuk / Log Keluar -->
                <div class="flex items-center gap-2 border-b border-white/5 pb-3">
                    <a href="{{ route('user_access_logs.index', array_merge(request()->except('event_type'), ['event_type' => ''])) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('event_type') ? 'bg-purple-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        Semua Aktiviti
                    </a>
                    <a href="{{ route('user_access_logs.index', array_merge(request()->except('event_type'), ['event_type' => 'login'])) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('event_type') === 'login' ? 'bg-green-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        🔑 Log Masuk Sahaja
                    </a>
                    <a href="{{ route('user_access_logs.index', array_merge(request()->except('event_type'), ['event_type' => 'logout'])) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('event_type') === 'logout' ? 'bg-red-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        🚪 Log Keluar Sahaja
                    </a>
                </div>

                <form method="GET" action="{{ route('user_access_logs.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
                    <input type="hidden" name="event_type" value="{{ request('event_type') }}">
                    
                    <!-- Search Input -->
                    <div class="relative flex-grow">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isAdmin ? 'Cari nama pengguna, e-mel, atau IP...' : 'Cari alamat IP...' }}"
                            class="w-full rounded-xl bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 pl-11 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>

                    <!-- Per Page Option Dropdown (50, 100, 200, 300) -->
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-xs text-gray-400 font-semibold whitespace-nowrap">Paparan Rekod:</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()" class="rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 cursor-pointer">
                            @foreach($allowedPerPage as $option)
                                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                    {{ $option }} Rekod / Halaman
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shrink-0">
                            Cari
                        </button>

                        @if(request()->hasAny(['search', 'per_page', 'event_type']))
                            <a href="{{ route('user_access_logs.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition border border-white/10 rounded-xl hover:bg-white/5 text-center shrink-0">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- User Access Logs Table -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5 bg-white/5">
                                <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider"># ID</th>
                                @if($isAdmin)
                                    <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Pengguna</th>
                                @endif
                                <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Aktiviti Sesi</th>
                                <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Alamat IP</th>
                                <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Peranti / Pelayar</th>
                                <th class="text-center px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Tarikh & Masa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($accessLogs as $log)
                                @php $badge = $log->event_badge; @endphp
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                        #{{ $log->id }}
                                    </td>
                                    @if($isAdmin)
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-white text-sm">{{ $log->user_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $log->user_email }}</div>
                                        </td>
                                    @endif
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border {{ $badge['bg'] }}">
                                            <span>{{ $badge['icon'] }}</span>
                                            <span>{{ $badge['label'] }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-gray-300 font-mono text-xs font-bold">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            {{ $log->ip_address }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-700/50 text-gray-300 font-medium">
                                            💻 {{ $log->browser_summary }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-xs font-semibold text-white">
                                            {{ $log->created_at->format('d/m/Y h:i:s A') }}
                                        </div>
                                        <div class="text-[10px] text-gray-500">
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-6 py-12 text-center text-gray-500">
                                        Tiada rekod sesi log masuk atau keluar ditemui.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($accessLogs->hasPages())
                    <div class="px-6 py-4 border-t border-white/5 bg-gray-900/40">
                        {{ $accessLogs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
