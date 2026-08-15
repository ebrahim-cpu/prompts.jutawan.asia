<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Pengguna</h2>
                <p class="text-sm text-gray-400 mt-1">Kemaskini maklumat, role dan tier pengguna.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-gray-300 text-sm font-medium rounded-xl hover:bg-white/20 transition border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-5 py-4 rounded-xl">
                    <svg class="w-5 h-5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            {{-- User Info Card --}}
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-purple-500/20">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-400">{{ $user->email }}</p>
                        <p class="text-xs text-gray-500 mt-1">Berdaftar: {{ $user->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-300 mb-2">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-300 mb-2">Emel</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-bold text-gray-300 mb-2">Role</label>
                        <select name="role" id="role" class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>👤 User — Pengguna biasa</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>🛡️ Admin — Akses penuh</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p class="text-xs text-amber-400 mt-1">⚠️ Anda tidak boleh menukar role anda sendiri.</p>
                        @endif
                        @error('role') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{ 
                            tier: '{{ old('tier', $user->tier) }}', 
                            startsAt: '{{ old('subscription_starts_at', $user->subscription_starts_at ? $user->subscription_starts_at->format('Y-m-d\TH:i') : '') }}',
                            expiresAt: '{{ old('premium_expires_at', $user->premium_expires_at ? $user->premium_expires_at->format('Y-m-d\TH:i') : '') }}',
                            addDays(days) {
                                let start = this.startsAt ? new Date(this.startsAt) : new Date();
                                if (!this.startsAt) {
                                    start.setMinutes(start.getMinutes() - start.getTimezoneOffset());
                                    this.startsAt = start.toISOString().slice(0, 16);
                                }
                                let end = new Date(start);
                                end.setDate(end.getDate() + days);
                                this.expiresAt = end.toISOString().slice(0, 16);
                            },
                            setLifetime() {
                                let start = new Date();
                                start.setMinutes(start.getMinutes() - start.getTimezoneOffset());
                                this.startsAt = start.toISOString().slice(0, 16);
                                this.expiresAt = ''; // lifetime
                            }
                        }">
                        <label for="tier" class="block text-sm font-bold text-gray-300 mb-2">Tier Langganan</label>
                        <select name="tier" id="tier" x-model="tier" class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <option value="free">🆓 Free — Akses terhad</option>
                            <option value="premium">⭐ Premium — Akses penuh</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($user->tier === 'premium' && $user->premium_expires_at)
                                Premium aktif hingga: <strong class="text-gray-300">{{ $user->premium_expires_at->format('d M Y') }}</strong>
                                @if($user->premium_expires_at->isPast()) <span class="text-red-400">(Tamat)</span>
                                @elseif($user->isSubscriptionExpiringSoon()) <span class="text-yellow-400">(Tamat dalam h-{{ $user->daysUntilExpiry() }})</span>
                                @else <span class="text-green-400">(Aktif)</span> @endif
                            @elseif($user->tier === 'premium')
                                ✅ Akses Seumur Hidup
                            @else
                                Belum pernah melanggan Premium.
                            @endif
                        </p>
                        @error('tier') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <!-- Subscription Dates (Only show if tier is Premium) -->
                        <div x-show="tier === 'premium'" x-collapse class="mt-4 p-4 rounded-xl bg-purple-500/5 border border-purple-500/20 space-y-4">
                            <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider">Durasi Langganan Premium</h4>
                            
                            <!-- Quick actions -->
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="addDays(30)" class="px-2.5 py-1 text-xs bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 rounded-lg transition">+30 Hari</button>
                                <button type="button" @click="addDays(90)" class="px-2.5 py-1 text-xs bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 rounded-lg transition">+90 Hari</button>
                                <button type="button" @click="addDays(365)" class="px-2.5 py-1 text-xs bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 rounded-lg transition">+1 Tahun</button>
                                <button type="button" @click="setLifetime()" class="px-2.5 py-1 text-xs bg-yellow-500/20 text-yellow-300 hover:bg-yellow-500/30 rounded-lg transition">Seumur Hidup</button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label for="subscription_starts_at" class="block text-xs font-medium text-gray-400 mb-1">Tarikh Mula</label>
                                    <input type="datetime-local" name="subscription_starts_at" id="subscription_starts_at" x-model="startsAt"
                                        class="block w-full rounded-lg bg-gray-900 border-gray-700 text-white px-3 py-2 text-sm focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition">
                                    @error('subscription_starts_at') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="premium_expires_at" class="block text-xs font-medium text-gray-400 mb-1">Tarikh Tamat (Biarkan kosong = Seumur Hidup)</label>
                                    <input type="datetime-local" name="premium_expires_at" id="premium_expires_at" x-model="expiresAt"
                                        class="block w-full rounded-lg bg-gray-900 border-gray-700 text-white px-3 py-2 text-sm focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition">
                                    @error('premium_expires_at') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-white transition">Batal</a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            @if($user->id !== auth()->id())
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-red-500/30 p-6">
                <h3 class="text-sm font-bold text-red-400 mb-2 flex items-center gap-2">⚠️ Zon Bahaya</h3>
                <p class="text-xs text-gray-500 mb-4">Memadam pengguna adalah tindakan kekal dan tidak boleh dibatalkan.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('AMARAN: Padam pengguna {{ $user->name }}? Tindakan ini KEKAL.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Padam Pengguna Ini
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
