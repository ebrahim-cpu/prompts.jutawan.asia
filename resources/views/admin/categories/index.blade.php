<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Pengurusan Kategori 📂</h2>
                <p class="text-sm text-gray-400 mt-1">Tambah, kemaskini dan padam kategori prompt.</p>
            </div>
            <a href="{{ route('admin.prompts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white text-sm font-medium rounded-xl hover:bg-white/20 transition border border-white/10">
                ← Urus Prompt
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, editMode: false, formAction: '{{ route('admin.categories.store') }}', catName: '', catIcon: '🎨', catColor: 'purple', catId: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Header Action Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-bold text-white">Jumlah Kategori: {{ $totalCategories }}</p>
                    <p class="text-xs text-gray-400">Semua kategori ini sedia ada untuk dipilih oleh pengguna semasa membuat/mengedit prompt.</p>
                </div>
                <button type="button" @click="editMode = false; catName = ''; catIcon = '🎨'; catColor = 'purple'; formAction = '{{ route('admin.categories.store') }}'; showModal = true;"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/20">
                    + Tambah Kategori Baru
                </button>
            </div>

            <!-- Categories Grid/Table -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-white/5 bg-white/5 text-xs text-gray-400 uppercase font-bold">
                                <th class="px-6 py-4">Ikon & Nama Kategori</th>
                                <th class="px-6 py-4">Slug</th>
                                <th class="px-6 py-4 text-center">Jumlah Prompt</th>
                                <th class="px-6 py-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($categories as $cat)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 font-bold text-white flex items-center gap-3">
                                        <span class="text-2xl p-2 rounded-xl bg-white/5 border border-white/10">{{ $cat->icon ?: '🎨' }}</span>
                                        <span>{{ $cat->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-purple-400">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-300">
                                        <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs">
                                            {{ $cat->prompts_count }} Prompt
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                    @click="editMode = true; catId = {{ $cat->id }}; catName = '{{ addslashes($cat->name) }}'; catIcon = '{{ addslashes($cat->icon) }}'; catColor = '{{ addslashes($cat->color) }}'; formAction = '/admin/categories/' + {{ $cat->id }}; showModal = true;"
                                                    class="p-2 text-gray-400 hover:text-purple-400 rounded-lg hover:bg-white/10 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            
                                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirmDelete(this, 'Adakah anda pasti mahu memadam kategori ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-400 rounded-lg hover:bg-white/10 transition" title="Padam">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        Tiada kategori ditemui.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Add/Edit Category Modal -->
        <template x-teleport="body">
            <div x-show="showModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative z-10 w-full max-w-md bg-gray-800 border border-white/10 rounded-2xl p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-white mb-4" x-text="editMode ? 'Kemaskini Kategori' : 'Tambah Kategori Baru'"></h3>
                    
                    <form :action="formAction" method="POST" class="space-y-4">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-300 mb-1">Nama Kategori</label>
                            <input type="text" id="name" name="name" x-model="catName" required placeholder="cth: Realistik"
                                class="w-full rounded-xl bg-gray-900 border-gray-700 text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500">
                        </div>

                        <div>
                            <label for="icon" class="block text-xs font-bold text-gray-300 mb-1">Ikon / Emoji</label>
                            <div class="flex gap-2 mb-2">
                                <input type="text" id="icon" name="icon" x-model="catIcon" placeholder="cth: 📷"
                                    class="w-full rounded-xl bg-gray-900 border-gray-700 text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500">
                                <div class="w-12 h-10 shrink-0 flex items-center justify-center bg-gray-900 border border-gray-700 rounded-xl text-2xl" x-text="catIcon || '🎨'"></div>
                            </div>
                            <p class="text-xs text-gray-400 mb-2">Klik preset di bawah atau tekan <kbd class="px-1.5 py-0.5 bg-gray-700 text-gray-200 rounded text-[10px] font-mono">Win</kbd> + <kbd class="px-1.5 py-0.5 bg-gray-700 text-gray-200 rounded text-[10px] font-mono">.</kbd> (Windows) / <kbd class="px-1.5 py-0.5 bg-gray-700 text-gray-200 rounded text-[10px] font-mono">Cmd</kbd> + <kbd class="px-1.5 py-0.5 bg-gray-700 text-gray-200 rounded text-[10px] font-mono">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-gray-700 text-gray-200 rounded text-[10px] font-mono">Space</kbd> (Mac):</p>
                            <div class="flex flex-wrap gap-1.5 p-2 bg-gray-900/60 rounded-xl border border-white/5 max-h-28 overflow-y-auto">
                                <template x-for="e in ['🎨', '📷', '🚀', '💡', '💼', '🤖', '✍️', '🎬', '🎵', '🌐', '📦', '🔥', '👑', '💬', '⚡', '🧠', '🔮', '🎯', '✨', '📝', '📊', '🛠️', '💻', '📈', '🎓', '🏆', '❤️', '🌟']">
                                    <button type="button" @click="catIcon = e" class="p-1.5 hover:bg-white/10 rounded-lg text-xl transition flex items-center justify-center" :class="catIcon === e ? 'bg-purple-500/30 border border-purple-500/50' : ''" x-text="e"></button>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-sm rounded-xl hover:opacity-90 transition">
                                Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
