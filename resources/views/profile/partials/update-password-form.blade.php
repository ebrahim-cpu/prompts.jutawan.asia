<section>
    <header>
        <h2 class="text-lg font-bold text-white">
            Kemaskini Kata Laluan 🔒
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            Pastikan akaun anda menggunakan kata laluan yang panjang dan selamat.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Kata Laluan Semasa" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white rounded-xl" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Kata Laluan Baru" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Sahkan Kata Laluan Baru" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-2.5 px-6 rounded-xl hover:opacity-90 transition border-0">Simpan Kata Laluan</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-400 font-semibold"
                >✓ Kata Laluan Dikemaskini!</p>
            @endif
        </div>
    </form>
</section>
