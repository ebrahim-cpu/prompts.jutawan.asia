<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Pengurusan Hashtag #️⃣</h2>
                <p class="text-sm text-gray-400 mt-1">Tambah, kemaskini dan padam hashtag/tag prompt.</p>
            </div>
            <a href="{{ route('admin.prompts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white text-sm font-medium rounded-xl hover:bg-white/20 transition border border-white/10">
                ← Urus Prompt
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, editMode: false, formAction: '{{ route('admin.tags.store') }}', tagName: '', tagId: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Header Action Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-bold text-white">Jumlah Hashtag: {{ $totalTags }}</p>
                    <p class="text-xs text-gray-400">Hashtag yang dicipta di sini boleh dipilih secara berbilang (multiple select) semasa menambah/mengedit prompt.</p>
                </div>
                <button type="button" @click="editMode = false; tagName = ''; formAction = '{{ route('admin.tags.store') }}'; showModal = true;"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-pink-600 to-purple-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-pink-500/20">
                    + Tambah Hashtag Baru
                </button>
            </div>

            <!-- Tags Table -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-white/5 bg-white/5 text-xs text-gray-400 uppercase font-bold">
                                <th class="px-6 py-4">Nama Hashtag</th>
                                <th class="px-6 py-4">Slug</th>
                                <th class="px-6 py-4 text-center">Penggunaan Dalam Prompt</th>
                                <th class="px-6 py-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($tags as $t)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 font-bold text-white">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-pink-500/10 text-pink-400 border border-pink-500/20 font-mono text-xs">
                                            #{{ $t->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-400">
                                        {{ $t->slug }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-300">
                                        <span class="px-3 py-1 rounded-full bg-white/5 text-gray-300 border border-white/10 text-xs">
                                            {{ $t->usage_count }} Prompt
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                    @click="editMode = true; tagId = {{ $t->id }}; tagName = '{{ addslashes($t->name) }}'; formAction = '/admin/tags/' + {{ $t->id }}; showModal = true;"
                                                    class="p-2 text-gray-400 hover:text-pink-400 rounded-lg hover:bg-white/10 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            
                                            <form action="{{ route('admin.tags.destroy', $t->id) }}" method="POST" onsubmit="return confirmDelete(this, 'Adakah anda pasti mahu memadam hashtag ini?');">
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
                                        Tiada hashtag ditemui.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tags->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $tags->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Add/Edit Tag Modal -->
        <template x-teleport="body">
            <div x-show="showModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative z-10 w-full max-w-md bg-gray-800 border border-white/10 rounded-2xl p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-white mb-4" x-text="editMode ? 'Kemaskini Hashtag' : 'Tambah Hashtag Baru'"></h3>
                    
                    <form :action="formAction" method="POST" class="space-y-4">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-300 mb-1">Nama Hashtag</label>
                            <input type="text" id="name" name="name" x-model="tagName" required placeholder="cth: cyberpunk"
                                class="w-full rounded-xl bg-gray-900 border-gray-700 text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-pink-500">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-pink-600 to-purple-600 text-white font-bold text-sm rounded-xl hover:opacity-90 transition">
                                Simpan Hashtag
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
