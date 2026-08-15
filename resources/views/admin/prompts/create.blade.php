<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.prompts.index') }}" class="p-2 rounded-xl hover:bg-white/10 transition text-gray-400 hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">Tambah Prompt Baru</h2>
                <p class="text-sm text-gray-400 mt-1">Isi maklumat di bawah untuk menambah prompt AI baru ke dalam koleksi.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
                <form action="{{ route('admin.prompts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-8 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-bold text-gray-300 mb-2">Tajuk Prompt</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="cth: Cyberpunk City Skyline"
                                class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" required>
                            @error('title') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-300 mb-2">Penerangan Ringkas <span class="text-gray-500 font-normal">(Pilihan)</span></label>
                            <textarea name="description" id="description" rows="2" placeholder="Penerangan singkat tentang prompt ini..."
                                class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">{{ old('description') }}</textarea>
                            @error('description') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category (Single Selection Only) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="category" class="block text-sm font-bold text-gray-300">Kategori <span class="text-xs text-purple-400 font-normal">(Pilih 1 Kategori Sahaja)</span></label>
                                <a href="{{ route('admin.categories.index') }}" target="_blank" class="text-xs text-purple-400 hover:underline flex items-center gap-1">
                                    ⚙️ Urus Kategori
                                </a>
                            </div>
                            <select name="category" id="category" class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition cursor-pointer">
                                @foreach(\App\Models\Category::orderBy('name', 'asc')->get() as $cat)
                                    <option value="{{ $cat->slug }}" {{ old('category', 'general') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->icon ?: '🎨' }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Rating -->
                        <div x-data="{ rating: {{ old('rating', 3) }} }">
                            <label class="block text-sm font-bold text-gray-300 mb-2">Rating Kualiti</label>
                            <div class="flex items-center gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" @click="rating = star" class="p-0.5 transition-transform hover:scale-125">
                                        <svg class="w-8 h-8 transition-colors" :class="star <= rating ? 'text-yellow-400' : 'text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </template>
                                <span class="ml-3 text-sm text-gray-400" x-text="rating + '/5'"></span>
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            @error('rating') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tags (Multiple Selection Allowed) -->
                        @php
                            $dbTagNames = \App\Models\Tag::pluck('name')->toArray();
                            $allUsedTags = array_keys(\App\Models\Prompt::allTags());
                            
                            $rawSelected = old('tags') !== null 
                                ? array_filter(array_map(fn($t) => ltrim(trim($t), '#'), explode(',', old('tags')))) 
                                : [];
                            
                            $selectedTags = array_values(array_unique(array_filter(array_map(
                                fn($t) => ltrim(trim($t), '#'), 
                                $rawSelected
                            ))));

                            $mergedAvailable = array_values(array_unique(array_filter(array_map(
                                fn($t) => ltrim(trim($t), '#'), 
                                array_merge($dbTagNames, $allUsedTags, $selectedTags)
                            ))));
                            natcasesort($mergedAvailable);
                            $mergedAvailable = array_values($mergedAvailable);
                        @endphp
                        <!-- Collapsible Tag Control Block -->
                        <div x-data="{
                            isCollapsed: true,
                            selectedTags: {{ json_encode($selectedTags) }},
                            availableTags: {{ json_encode($mergedAvailable) }},
                            customTag: '',
                            init() {
                                this.selectedTags.forEach(tag => {
                                    let clean = tag.trim().replace(/^#/, '');
                                    if (clean && !this.availableTags.some(a => a.toLowerCase() === clean.toLowerCase())) {
                                        this.availableTags.push(clean);
                                    }
                                });
                                this.availableTags.sort((a, b) => a.localeCompare(b, undefined, {sensitivity: 'base'}));
                            },
                            hasTag(tagName) {
                                let clean = tagName.trim().replace(/^#/, '').toLowerCase();
                                return this.selectedTags.some(t => t.trim().replace(/^#/, '').toLowerCase() === clean);
                            },
                            toggleTag(tagName) {
                                let clean = tagName.trim().replace(/^#/, '');
                                if (this.hasTag(clean)) {
                                    this.selectedTags = this.selectedTags.filter(t => t.trim().replace(/^#/, '').toLowerCase() !== clean.toLowerCase());
                                } else {
                                    this.selectedTags.push(clean);
                                }
                            },
                            addCustomTag() {
                                let tag = this.customTag.trim().replace(/^#/, '');
                                if (tag && !this.hasTag(tag)) {
                                    this.selectedTags.push(tag);
                                    if (!this.availableTags.some(a => a.toLowerCase() === tag.toLowerCase())) {
                                        this.availableTags.push(tag);
                                    }
                                    this.availableTags.sort((a, b) => a.localeCompare(b, undefined, {sensitivity: 'base'}));
                                }
                                this.customTag = '';
                            },
                            removeTag(tag) {
                                this.toggleTag(tag);
                            }
                        }" class="border border-white/10 rounded-2xl bg-gray-900/40 overflow-hidden shadow-lg transition-all">

                            <!-- Header Bar (Clickable to Collapse / Expand) -->
                            <div class="px-5 py-4 bg-gray-900/80 border-b border-white/5 flex items-center justify-between flex-wrap gap-3">
                                <div class="flex items-center gap-3 cursor-pointer select-none" @click="isCollapsed = !isCollapsed">
                                    <button type="button" class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition" title="Kuncup/Kembangkan Panel">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="isCollapsed ? '-rotate-90' : 'rotate-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div>
                                        <label class="text-sm font-bold text-white cursor-pointer flex items-center gap-2">
                                            🏷️ Tag Prompt
                                            <span class="text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-full bg-pink-500/20 text-pink-300 border border-pink-500/30"
                                                  x-text="selectedTags.length + ' tag dipilih'"></span>
                                        </label>
                                        <p class="text-[11px] text-gray-400 mt-0.5">Pilih atau tambah tag untuk carian prompt.</p>
                                    </div>
                                </div>

                                <!-- Action Links -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.tags.index') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-pink-500/20 hover:bg-pink-500/30 text-pink-300 border border-pink-500/30 text-xs font-bold transition flex items-center gap-1">
                                        ⚙️ Urus Tag
                                    </a>
                                </div>
                            </div>

                            <!-- Hidden Input for Laravel Form -->
                            <input type="hidden" name="tags" :value="selectedTags.join(', ')">

                            <!-- Collapsible Content Area -->
                            <div x-show="!isCollapsed" x-transition.duration.200ms class="p-5 space-y-4">
                                <!-- AVAILABLE TAG CHIPS / PILLS -->
                                <div>
                                    <span class="block text-xs font-semibold text-gray-400 mb-2">Senarai Tag Sedia Ada:</span>
                                    <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-1 custom-scrollbar">
                                        <template x-for="tagName in availableTags" :key="tagName">
                                            <button type="button" 
                                                    @click="toggleTag(tagName)"
                                                    :class="hasTag(tagName) 
                                                        ? 'bg-gradient-to-r from-pink-600 to-purple-600 text-white font-bold border-pink-400 shadow-md shadow-pink-500/20 scale-105' 
                                                        : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-white'"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-mono border transition-all duration-200 flex items-center gap-1 cursor-pointer">
                                                <span x-text="tagName"></span>
                                                <span x-show="hasTag(tagName)" class="text-xs font-bold">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Add Custom Tag On-The-Fly -->
                                <div class="flex items-center gap-2 pt-3 border-t border-white/5">
                                    <input type="text" 
                                           x-model="customTag" 
                                           @keydown.enter.prevent="addCustomTag()"
                                           placeholder="Taip tag baru..." 
                                           class="rounded-xl bg-gray-950 border-gray-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-pink-500 flex-grow">
                                    <button type="button" 
                                            @click="addCustomTag()"
                                            class="px-4 py-2 bg-pink-500/20 text-pink-300 hover:bg-pink-500/30 rounded-xl text-xs font-bold transition shrink-0 cursor-pointer">
                                        + Tambah Tag
                                    </button>
                                </div>

                                <p class="text-xs text-gray-500 mt-1">Klik tag di atas untuk memilih (tandakan ✓). Anda boleh memilih seberapa banyak tag yang diperlukan.</p>
                            </div>
                        </div>
                        @error('tags') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <!-- Collapsible Prompt Text Block with Trim & Copy Buttons -->
                        <div x-data="{ 
                            isCollapsed: true, 
                            promptText: {{ json_encode(old('prompt_text', '')) }},
                            isTrimmed: false,
                            copied: false,
                            trimPrompt() {
                                if (!this.promptText) return;
                                let cleaned = this.promptText
                                    .replace(/[\r\n]+/g, ' ')
                                    .replace(/\s+/g, ' ')
                                    .trim();
                                this.promptText = cleaned;
                                this.isTrimmed = true;
                                setTimeout(() => this.isTrimmed = false, 2500);
                            },
                            copyPrompt() {
                                if (!this.promptText) return;
                                navigator.clipboard.writeText(this.promptText);
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2500);
                            }
                        }" class="border border-white/10 rounded-2xl bg-gray-900/40 overflow-hidden shadow-lg transition-all">

                            <!-- Header Bar (Clickable to Collapse / Expand) -->
                            <div class="px-5 py-4 bg-gray-900/80 border-b border-white/5 flex items-center justify-between flex-wrap gap-3">
                                <div class="flex items-center gap-3 cursor-pointer select-none" @click="isCollapsed = !isCollapsed">
                                    <button type="button" class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition" title="Kuncup/Kembangkan Panel">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="isCollapsed ? '-rotate-90' : 'rotate-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div>
                                        <label class="text-sm font-bold text-white cursor-pointer flex items-center gap-2">
                                            📝 Teks Prompt Sebenar
                                            <span class="text-[10px] font-mono font-medium px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-gray-400"
                                                  x-text="(promptText ? promptText.length : 0) + ' aksara | ' + (promptText && promptText.trim() ? promptText.trim().split(/\s+/).length : 0) + ' perkataan'"></span>
                                        </label>
                                        <p class="text-[11px] text-gray-400 mt-0.5">Ketik atau sunting teks prompt yang akan disalin pengguna.</p>
                                    </div>
                                </div>

                                <!-- Action Buttons (Copy & Trim) -->
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            @click="copyPrompt()" 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-sm hover:scale-105 active:scale-95 cursor-pointer border"
                                            :class="copied ? 'bg-green-500/20 text-green-300 border-green-500/30' : 'bg-pink-500/20 hover:bg-pink-500/30 text-pink-300 border-pink-500/30'"
                                            title="Salin teks prompt ke clipboard">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="!copied" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            <path x-show="copied" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span x-text="copied ? '✓ Disalin!' : '📋 Salin Prompt'"></span>
                                    </button>

                                    <button type="button" 
                                            @click="trimPrompt()" 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/30 text-xs font-bold transition shadow-sm hover:scale-105 active:scale-95 cursor-pointer"
                                            title="Buang ruang kosong berlebihan dan kemaskan format teks">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 0L4 4m5.121 5.121L4 14.121"/></svg>
                                        <span>✂️ Trim Prompt</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Collapsible Content Area -->
                            <div x-show="!isCollapsed" x-transition.duration.200ms class="p-5 space-y-3">
                                <div class="relative">
                                    <textarea name="prompt_text" 
                                              id="prompt_text" 
                                              rows="6" 
                                              x-model="promptText" 
                                              placeholder="cyberpunk cityscape, neon lights, rainy streets..."
                                              class="block w-full rounded-xl bg-gray-950/70 border-gray-700 text-white placeholder-gray-500 px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition leading-relaxed" 
                                              required></textarea>
                                </div>

                                <!-- Feedback Toast when Trimmed -->
                                <div x-show="isTrimmed" x-cloak x-transition class="p-2.5 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-xs flex items-center justify-between">
                                    <span class="flex items-center gap-1.5 font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Teks prompt berjaya dibersihkan & di-trim!
                                    </span>
                                    <span class="text-[10px] text-green-300 font-mono">Ruang kosong berlebihan dibuang</span>
                                </div>

                                <!-- Feedback Toast when Copied -->
                                <div x-show="copied" x-cloak x-transition class="p-2.5 rounded-xl bg-pink-500/10 border border-pink-500/30 text-pink-300 text-xs flex items-center justify-between">
                                    <span class="flex items-center gap-1.5 font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Teks prompt berjaya disalin ke clipboard!
                                    </span>
                                    <span class="text-[10px] text-pink-200 font-mono">Sedia untuk ditampal</span>
                                </div>

                                <p class="text-[11px] text-gray-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Klik <strong>"📋 Salin Prompt"</strong> untuk menyalin teks atau <strong>"✂️ Trim Prompt"</strong> untuk membuang spacing/enter berlebihan.
                                </p>
                            </div>
                        </div>
                        @error('prompt_text') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <!-- Drag & Drop Multiple Image Upload Box -->
                        <div x-data="{
                            isDragging: false,
                            previews: [],
                            filesList: new DataTransfer(),
                            addFiles(files) {
                                for (let f of files) {
                                    if (!f.type.startsWith('image/')) continue;
                                    this.filesList.items.add(f);
                                    let r = new FileReader();
                                    r.onload = e => {
                                        this.previews.push({
                                            name: f.name,
                                            size: (f.size / 1024 / 1024).toFixed(2) + ' MB',
                                            src: e.target.result
                                        });
                                    };
                                    r.readAsDataURL(f);
                                }
                                $refs.fileInput.files = this.filesList.files;
                            },
                            removeFile(index) {
                                this.previews.splice(index, 1);
                                let dt = new DataTransfer();
                                let currentFiles = this.filesList.files;
                                for (let i = 0; i < currentFiles.length; i++) {
                                    if (i !== index) {
                                        dt.items.add(currentFiles[i]);
                                    }
                                }
                                this.filesList = dt;
                                $refs.fileInput.files = dt.files;
                            },
                            handleDrop(e) {
                                this.isDragging = false;
                                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                                    this.addFiles(e.dataTransfer.files);
                                }
                            }
                        }">
                            <label class="block text-sm font-bold text-gray-300 mb-2">
                                Gambar Contoh <span class="text-gray-500 font-normal">(Boleh Seret & Lepas Pelbagai Gambar)</span>
                            </label>

                            <!-- Dropzone Box -->
                            <div 
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop($event)"
                                @click="$refs.fileInput.click()"
                                :class="isDragging ? 'border-purple-500 bg-purple-500/10 scale-[1.01] shadow-xl shadow-purple-500/20' : 'border-gray-700 bg-gray-900/40 hover:border-purple-500/60 hover:bg-gray-900/60'"
                                class="relative border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all duration-200 group">

                                <input 
                                    x-ref="fileInput"
                                    id="images" 
                                    name="images[]" 
                                    type="file" 
                                    multiple 
                                    accept="image/*" 
                                    class="hidden"
                                    @change="addFiles($event.target.files)">

                                <div class="space-y-3 pointer-events-none">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-purple-500/10 flex items-center justify-center border border-purple-500/20 group-hover:scale-110 transition-transform">
                                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold text-white mb-1">
                                            <span x-show="!isDragging">Seret & Lepas Gambar Di Sini</span>
                                            <span x-show="isDragging" class="text-purple-400">Lepaskan Gambar Sekarang! 🎉</span>
                                        </p>
                                        <p class="text-xs text-gray-400">atau <span class="text-purple-400 font-semibold underline">Klik untuk Pilih Fail</span> dari peranti anda</p>
                                    </div>

                                    <p class="text-[11px] text-gray-500">Format: PNG, JPG, JPEG, WEBP, GIF (Saiz Maksimum: <strong>10MB</strong> setiap gambar)</p>
                                </div>
                            </div>

                            <!-- Preview & Removal List -->
                            <template x-if="previews.length > 0">
                                <div class="mt-5">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-xs font-bold text-green-400 flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span x-text="previews.length + ' Gambar Dipilih untuk Dimuat Naik'"></span>
                                        </p>
                                        <button type="button" @click="previews = []; filesList = new DataTransfer(); $refs.fileInput.files = filesList.files;" class="text-[11px] text-red-400 hover:underline">
                                            Buang Semua
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                        <template x-for="(img, idx) in previews" :key="idx">
                                            <div class="relative group rounded-xl overflow-hidden ring-2 ring-purple-500/40 bg-gray-900 shadow-md">
                                                <img :src="img.src" class="w-full h-28 object-cover">
                                                
                                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-2 text-left">
                                                    <p class="text-[11px] font-medium text-white truncate" x-text="img.name"></p>
                                                    <p class="text-[9px] text-gray-400" x-text="img.size"></p>
                                                </div>

                                                <button type="button" @click="removeFile(idx)" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-red-600/90 text-white flex items-center justify-center hover:bg-red-500 transition shadow-lg" title="Buang Gambar Ini">
                                                    ✕
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            @error('images') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @error('images.*') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Is Premium -->
                        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4">
                            <label for="is_premium" class="flex items-center gap-3 cursor-pointer">
                                <input id="is_premium" name="is_premium" type="checkbox" value="1" {{ old('is_premium') ? 'checked' : '' }}
                                    class="h-5 w-5 rounded text-yellow-500 border-yellow-600 bg-gray-900 focus:ring-yellow-500 transition">
                                <div>
                                    <span class="font-bold text-yellow-300 text-sm">⭐ Tandakan sebagai Premium</span>
                                    <p class="text-xs text-gray-400 mt-0.5">Hanya untuk pengguna bertier Premium sahaja. Pengguna Free akan nampak teks prompt dikaburkan.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-900/50 px-8 py-4 flex items-center justify-between border-t border-white/10">
                        <a href="{{ route('admin.prompts.index') }}" class="text-sm text-gray-400 hover:text-gray-200 transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-purple-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Prompt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
