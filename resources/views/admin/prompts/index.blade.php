<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Urus Semua Prompt</h2>
                <p class="text-sm text-gray-400 mt-1">Tambah, edit atau buang prompt AI dari koleksi anda.</p>
            </div>
            <a href="{{ route('admin.prompts.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Prompt Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 text-center">
                    <div class="text-3xl font-extrabold text-white">{{ $totalPrompts }}</div>
                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Jumlah Prompt</div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 text-center">
                    <div class="text-3xl font-extrabold text-green-400">{{ $freePrompts }}</div>
                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Free</div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 text-center">
                    <div class="text-3xl font-extrabold text-yellow-400">{{ $premiumPrompts }}</div>
                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-medium">Premium</div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-4 rounded-xl" role="alert" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            @endif

            @if($prompts->isEmpty())
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-16 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-700 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-300 mb-2">Tiada Prompt Lagi</h3>
                    <p class="text-sm text-gray-500 mb-6">Mulakan dengan mencipta prompt pertama anda.</p>
                    <a href="{{ route('admin.prompts.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Cipta Prompt Pertama
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($prompts as $prompt)
                        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 hover:border-white/20 transition p-5">
                            <div class="flex items-center gap-5">
                                <!-- Image Preview -->
                                <div class="shrink-0">
                                    @php $firstImg = $prompt->getFirstImageUrl(); $imgCount = count($prompt->images ?? []); @endphp
                                    @if($firstImg)
                                        <div class="relative">
                                            <img src="{{ $firstImg }}" alt="{{ $prompt->title }}" class="w-20 h-20 object-cover rounded-xl ring-2 ring-white/10">
                                            @if($imgCount > 1)
                                                <span class="absolute -top-1.5 -right-1.5 bg-purple-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-gray-800">{{ $imgCount }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-xl flex items-center justify-center ring-2 ring-white/10">
                                            <svg class="w-8 h-8 text-indigo-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h4 class="font-bold text-white truncate">{{ $prompt->title }}</h4>
                                        @if($prompt->is_premium)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-400">⭐ Premium</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/20 text-green-400">Free</span>
                                        @endif
                                        <span class="text-xs text-yellow-400">@for($i = 0; $i < ($prompt->rating ?? 3); $i++)★@endfor</span>
                                    </div>
                                    <p class="text-sm text-gray-400 truncate">{{ $prompt->description ?? 'Tiada penerangan' }}</p>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        @php $catInfo = $prompt->getCategoryInfo(); @endphp
                                        <span class="text-[10px] text-gray-500 bg-gray-700/50 px-2 py-0.5 rounded">{{ $catInfo['icon'] }} {{ $catInfo['label'] }}</span>
                                        @if($prompt->tags)
                                            @foreach(array_slice($prompt->getTagsArray(), 0, 4) as $tag)
                                                <span class="text-[10px] text-pink-400/70 bg-pink-500/10 px-1.5 py-0.5 rounded">{{ $tag }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('admin.prompts.edit', $prompt->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-500/10 text-indigo-400 text-sm font-medium rounded-xl hover:bg-indigo-500/20 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.prompts.destroy', $prompt->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete(this, 'Adakah anda pasti mahu memadam prompt ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-500/10 text-red-400 text-sm font-medium rounded-xl hover:bg-red-500/20 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Padam
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $prompts->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
