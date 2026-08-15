<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-400">
            Padam Akaun ⚠️
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            Setelah akaun anda dipadamkan, semua data dan sumber akan dipadamkan secara kekal.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-500/20 text-red-400 hover:bg-red-500/30 border border-red-500/30 font-bold px-5 py-2.5 rounded-xl"
    >Padam Akaun Saya</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-gray-800 border border-white/10 text-white rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-white">
                Adakah anda pasti mahu memadamkan akaun anda?
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                Sila masukkan kata laluan anda untuk mengesahkan pemadaman akaun secara kekal.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Kata Laluan" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-white rounded-xl"
                    placeholder="Masukkan Kata Laluan Anda"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="bg-gray-700 text-gray-300 hover:bg-gray-600 rounded-xl">
                    Batal
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl">
                    Ya, Padam Akaun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
