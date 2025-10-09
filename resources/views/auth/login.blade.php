<x-guest-layout>
    <x-slot name="title">Masuk</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Selamat datang kembali</h2>
        <p class="mt-2 text-sm text-gray-600">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <input id="email"
                       name="email"
                       type="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="username"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="nama@email.com">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <input id="password"
                       name="password"
                       type="password"
                       required
                       autocomplete="current-password"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="Masukkan password Anda">
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me"
                       name="remember"
                       type="checkbox"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                <label for="remember_me" class="ml-3 text-sm leading-6 text-gray-900">
                    Ingat saya
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Masuk
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}"
                   class="font-semibold text-indigo-600 hover:text-indigo-500">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
