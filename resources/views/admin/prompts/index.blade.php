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

            <!-- Filter & Search Bar (Category, Tag, Search, and Pagination Options) -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 p-5 mb-6 relative z-30">
                <form method="GET" action="{{ route('admin.prompts.index') }}" id="adminPromptFilterForm" class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center justify-between">
                    
                    <!-- Search Input -->
                    <div class="relative flex-grow">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tajuk, penerangan, atau teks prompt..."
                            class="w-full rounded-xl bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 pl-11 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>

                    <!-- Category, Tag & Paging Controls -->
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                        <!-- Category Dropdown (ALL or Specific Category) -->
                        <select name="category" onchange="this.form.submit()" class="rounded-xl bg-gray-900/50 border-gray-700 text-white px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 cursor-pointer">
                            <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>📁 Semua Kategori (ALL)</option>
                            @foreach($dbCategories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->icon ?: '🎨' }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Tag Checkbox Selection Dropdown -->
                        <div class="relative" x-data="{ openTags: false }">
                            <button type="button" @click="openTags = !openTags" 
                                    class="px-3.5 py-2.5 rounded-xl bg-gray-900/50 border border-gray-700 text-white text-sm focus:ring-2 focus:ring-purple-500 cursor-pointer flex items-center gap-2">
                                <span>🏷️ Tag</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-pink-500/20 text-pink-300 border border-pink-500/30">
                                    {{ count($selectedTags) > 0 ? count($selectedTags) . ' dipilih' : 'Semua Tag' }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="openTags ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <!-- Checkbox Dropdown Box -->
                            <div x-show="openTags" x-cloak @click.away="openTags = false" x-transition 
                                 class="absolute left-0 mt-2 w-72 bg-gray-900 border border-white/10 rounded-2xl shadow-2xl p-4 z-[100] space-y-3">
                                <div class="flex items-center justify-between border-b border-white/10 pb-2">
                                    <span class="text-xs font-bold text-white">Pilih Tag (A-Z):</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                @click="document.querySelectorAll('input[name=\'tags[]\']').forEach(cb => cb.checked = false); document.getElementById('adminPromptFilterForm').submit();" 
                                                class="text-[11px] text-pink-400 hover:text-pink-300 font-bold hover:underline cursor-pointer flex items-center gap-1">
                                            <span>Untick All</span>
                                        </button>
                                        <span class="text-gray-600">|</span>
                                        <button type="button" @click="openTags = false" class="text-xs text-gray-400 hover:text-white">✕</button>
                                    </div>
                                </div>

                                <div class="max-h-60 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
                                    @foreach($allTags as $tagName => $tagCount)
                                        <label class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-white/5 cursor-pointer text-xs transition select-none">
                                            <input type="checkbox" name="tags[]" value="{{ $tagName }}" 
                                                   {{ in_array($tagName, $selectedTags) ? 'checked' : '' }}
                                                   onchange="this.form.submit()"
                                                   class="rounded border-gray-700 bg-gray-950 text-pink-500 shadow-sm focus:ring-pink-500">
                                            <span class="text-gray-200 font-medium">{{ $tagName }}</span>
                                            <span class="text-[10px] text-gray-500 ml-auto font-mono">({{ $tagCount }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Per Page Dropdown (50, 100, 150, 200, 300) -->
                        <select name="per_page" onchange="this.form.submit()" class="rounded-xl bg-gray-900/50 border-gray-700 text-white px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 cursor-pointer">
                            @foreach($allowedPerPage as $option)
                                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                    {{ $option }} Rekod / Halaman
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shrink-0">
                            Tapis
                        </button>

                        @if(request()->hasAny(['search', 'category', 'tag', 'tags', 'per_page']))
                            <a href="{{ route('admin.prompts.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition border border-white/10 rounded-xl hover:bg-white/5 text-center shrink-0">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
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
                        <div x-data="{ copied: false, copyPrompt() { navigator.clipboard.writeText({{ json_encode($prompt->prompt_text) }}); this.copied = true; setTimeout(() => this.copied = false, 2500); } }" 
                             class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 hover:border-white/20 transition p-5">
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
                                        <span class="text-[10px] text-gray-400 bg-gray-900/60 px-2 py-0.5 rounded border border-white/5" title="{{ $prompt->updated_at ? $prompt->updated_at->format('d/m/Y H:i') : '' }}">
                                            🕒 {{ $prompt->updated_at ? $prompt->updated_at->diffForHumans() : 'N/A' }}
                                        </span>
                                        @if($prompt->tags)
                                            @foreach(array_slice($prompt->getTagsArray(), 0, 4) as $tag)
                                                <span class="text-[10px] text-pink-400/70 bg-pink-500/10 px-1.5 py-0.5 rounded">{{ $tag }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="shrink-0 flex items-center gap-2">
                                    <button type="button" 
                                            @click="copyPrompt()" 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium rounded-xl transition cursor-pointer border"
                                            :class="copied ? 'bg-green-500/20 text-green-300 border-green-500/30' : 'bg-pink-500/10 hover:bg-pink-500/20 text-pink-400 border-pink-500/20'"
                                            title="Salin teks prompt ke clipboard">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="!copied" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            <path x-show="copied" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span x-text="copied ? 'Disalin!' : 'Salin'"></span>
                                    </button>

                                    <a href="{{ route('admin.prompts.edit', $prompt->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-500/10 text-indigo-400 text-sm font-medium rounded-xl hover:bg-indigo-500/20 transition border border-indigo-500/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.prompts.destroy', $prompt->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete(this, 'Adakah anda pasti mahu memadam prompt ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-500/10 text-red-400 text-sm font-medium rounded-xl hover:bg-red-500/20 transition border border-red-500/20">
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
