<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Tambah Pengguna Baru</h2>
                <p class="text-sm text-gray-400 mt-1">Daftar pengguna dengan role dan tier langganan.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-gray-300 text-sm font-medium rounded-xl hover:bg-white/20 transition border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-300 mb-2">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-300 mb-2">Emel</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-300 mb-2">Kata Laluan</label>
                            <input type="password" name="password" id="password" required
                                class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-300 mb-2">Sahkan Kata Laluan</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-bold text-gray-300 mb-2">Role</label>
                        <select name="role" id="role" class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>👤 User — Pengguna biasa</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>🛡️ Admin — Akses penuh</option>
                        </select>
                        @error('role') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{ 
                            tier: '{{ old('tier', 'free') }}', 
                            startsAt: '{{ old('subscription_starts_at') }}',
                            expiresAt: '{{ old('premium_expires_at') }}',
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Daftar Pengguna
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
