<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            Maklumat Profil & Gambar Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Kemaskini maklumat akaun anda dan muat naik gambar profil baru.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- User ID Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 text-xs font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
            ID Akaun Anda: #{{ $user->id }}
        </div>

        <!-- Profile Picture / Avatar Display & Upload -->
        <div>
            <x-input-label for="avatar" value="Gambar Profil" class="mb-2" />

            <div class="flex items-center gap-6">
                <!-- Avatar Preview -->
                <div class="relative group w-20 h-20 rounded-full overflow-hidden border-2 border-purple-500/30 shadow-md bg-gray-100 flex-shrink-0">
                    @if ($user->avatar)
                        <img id="avatar-preview" src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div id="avatar-preview-placeholder" class="w-full h-full bg-gradient-to-br from-purple-500 to-pink-500 text-white font-bold text-2xl flex items-center justify-center">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <img id="avatar-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                    @endif
                </div>

                <!-- Upload Input -->
                <div class="flex-1">
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:me-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 transition cursor-pointer" onchange="previewAvatar(event)">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Format: JPG, PNG, GIF, WEBP (Maksimum 2MB). Disimpan ke folder: <code>uploads/users/{{ $user->id }}/</code>
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>
            </div>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Kemaskini') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-500 font-semibold"
                >✓ Profil & Gambar Berjaya Dikemaskini!</p>
            @endif
        </div>
    </form>

    <script>
        function previewAvatar(event) {
            const input = event.target;
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-preview-placeholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</section>
