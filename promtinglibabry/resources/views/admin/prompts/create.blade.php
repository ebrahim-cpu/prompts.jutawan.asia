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
                                @foreach(\App\Models\Category::all() as $cat)
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

                        <!-- Hashtags (Multiple Selection Allowed) -->
                        <div x-data="{
                            selectedTags: [],
                            availableTags: {{ json_encode(array_values(\App\Models\Tag::pluck('name')->toArray())) }},
                            customTag: '',
                            toggleTag(tagName) {
                                if (this.selectedTags.includes(tagName)) {
                                    this.selectedTags = this.selectedTags.filter(t => t !== tagName);
                                } else {
                                    this.selectedTags.push(tagName);
                                }
                            },
                            addCustomTag() {
                                let tag = this.customTag.trim().replace(/^#/, '');
                                if (tag && !this.selectedTags.includes(tag)) {
                                    this.selectedTags.push(tag);
                                    if (!this.availableTags.includes(tag)) {
                                        this.availableTags.push(tag);
                                    }
                                }
                                this.customTag = '';
                            }
                        }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-300">
                                    Hashtag / Tag <span class="text-xs text-pink-400 font-normal">(Boleh Pilih Lebih Dari Satu)</span>
                                </label>
                                <a href="{{ route('admin.tags.index') }}" target="_blank" class="text-xs text-pink-400 hover:underline flex items-center gap-1">
                                    ⚙️ Urus Hashtag
                                </a>
                            </div>

                            <!-- Hidden Input for Laravel Form -->
                            <input type="hidden" name="tags" :value="selectedTags.join(', ')">

                            <!-- Interactive Hashtag Chips / Pills -->
                            <div class="p-4 rounded-xl bg-gray-900/50 border border-gray-700 space-y-3">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tagName in availableTags" :key="tagName">
                                        <button type="button" 
                                                @click="toggleTag(tagName)"
                                                :class="selectedTags.includes(tagName) 
                                                    ? 'bg-gradient-to-r from-pink-600 to-purple-600 text-white font-bold border-pink-400 shadow-md shadow-pink-500/20 scale-105' 
                                                    : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-white'"
                                                class="px-3 py-1.5 rounded-xl text-xs font-mono border transition-all duration-200 flex items-center gap-1 cursor-pointer">
                                            <span x-text="'#' + tagName"></span>
                                            <span x-show="selectedTags.includes(tagName)" class="text-xs">✓</span>
                                        </button>
                                    </template>
                                </div>

                                <!-- Add Custom Hashtag On-The-Fly -->
                                <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                                    <input type="text" 
                                           x-model="customTag" 
                                           @keydown.enter.prevent="addCustomTag()"
                                           placeholder="Taip hashtag baru..." 
                                           class="rounded-lg bg-gray-900 border-gray-700 text-white text-xs px-3 py-1.5 focus:ring-1 focus:ring-pink-500 flex-grow">
                                    <button type="button" 
                                            @click="addCustomTag()"
                                            class="px-3 py-1.5 bg-pink-500/20 text-pink-300 hover:bg-pink-500/30 rounded-lg text-xs font-bold transition shrink-0">
                                        + Tambah Tag
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Klik hashtag di atas untuk memilih (tandakan ✓). Anda boleh memilih seberapa banyak hashtag yang diperlukan.</p>
                            @error('tags') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Prompt Text -->
                        <div>
                            <label for="prompt_text" class="block text-sm font-bold text-gray-300 mb-2">Teks Prompt Sebenar</label>
                            <textarea name="prompt_text" id="prompt_text" rows="5" placeholder="cyberpunk cityscape, neon lights, rainy streets..."
                                class="block w-full rounded-xl bg-gray-900/50 border-gray-700 text-white placeholder-gray-500 px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" required>{{ old('prompt_text') }}</textarea>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                Ini ialah teks yang pengguna akan salin. Pengguna Free hanya nampak versi kabur jika prompt ini ditanda Premium.
                            </p>
                            @error('prompt_text') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

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

                                    <p class="text-[11px] text-gray-500">Format: PNG, JPG, JPEG, WEBP, GIF (Boleh pilih atau seret banyak gambar serentak)</p>
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
