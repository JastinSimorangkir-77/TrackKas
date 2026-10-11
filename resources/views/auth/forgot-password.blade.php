<x-guest-layout>
    <div class="text-sm leading-relaxed text-gray-600">
        {{ __('Lupa password? Tidak masalah. Beri tahu alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email" class="block mt-2 w-full"
                            type="email"
                            name="email"
                            :value="old('email')"
                            placeholder="{{ __('Masukkan email Anda') }}"
                            required autofocus />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-auth-button class="mt-6">
            <x-slot:icon>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
            </x-slot:icon>

            {{ __('Kirim Tautan Reset') }}
        </x-auth-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        {{ __('Ingat password Anda?') }}
        <a class="rounded-md font-semibold text-brand-600 hover:text-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
           href="{{ route('login') }}">
            {{ __('Masuk sekarang') }}
        </a>
    </p>
</x-guest-layout>
