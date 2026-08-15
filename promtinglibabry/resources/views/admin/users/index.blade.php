<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Pengurusan Pengguna</h2>
                <p class="text-sm text-gray-400 mt-1">Urus semua pengguna berdaftar dalam sistem.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Pengguna
                </a>
                <a href="{{ route('admin.prompts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white text-sm font-medium rounded-xl hover:bg-white/20 transition border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Urus Prompt
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Toast --}}
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-4 rounded-xl" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-5 py-4 rounded-xl" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                    <button @click="show = false" class="ml-auto text-red-500 hover:text-red-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ $totalUsers }}</p>
                            <p class="text-xs text-gray-400">Jumlah Pengguna</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ $adminCount }}</p>
                            <p class="text-xs text-gray-400">Admin</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ $premiumCount }}</p>
                            <p class="text-xs text-gray-400">Premium</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 hover:border-white/20 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-white">{{ $freeCount }}</p>
                            <p class="text-xs text-gray-400">Free</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-grow">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau emel..."
                            class="w-full rounded-xl bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 pl-11 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <select name="role" class="rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 min-w-[130px]">
                        <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    <select name="tier" class="rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 min-w-[130px]">
                        <option value="all" {{ request('tier') === 'all' ? 'selected' : '' }}>Semua Tier</option>
                        <option value="free" {{ request('tier') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="premium" {{ request('tier') === 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium rounded-xl hover:opacity-90 transition shrink-0">
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'role', 'tier']))
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition border border-white/10 rounded-xl hover:bg-white/5 text-center shrink-0">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- User Table --}}
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Pengguna</th>
                                <th class="text-left px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Emel</th>
                                <th class="text-center px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Role</th>
                                <th class="text-center px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Tier</th>
                                <th class="text-center px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Premium Tamat</th>
                                <th class="text-center px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Daftar</th>
                                <th class="text-right px-6 py-4 font-bold text-gray-400 text-xs uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($users as $user)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-white">{{ $user->name }}</p>
                                                @if($user->id === auth()->id())
                                                    <span class="text-[10px] bg-blue-500/20 text-blue-400 font-bold px-1.5 py-0.5 rounded">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-400">🛡️ Admin</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400">User</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($user->tier === 'premium')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-400">⭐ Premium</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-700 text-gray-400">Free</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-500">
                                        @if($user->tier === 'premium' && $user->premium_expires_at)
                                            <span class="{{ $user->premium_expires_at->isPast() ? 'text-red-400' : 'text-green-400' }}">
                                                {{ $user->premium_expires_at->format('d/m/Y') }}
                                            </span>
                                        @elseif($user->tier === 'premium')
                                            <span class="text-green-400 font-semibold">Seumur Hidup</span>
                                        @else
                                            <span class="text-gray-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg text-indigo-400 hover:bg-indigo-500/10 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirmDelete(this, 'Adakah anda pasti mahu memadam pengguna {{ addslashes($user->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition" title="Padam">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Tiada pengguna ditemui.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
