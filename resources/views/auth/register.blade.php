<x-guest-layout>
    <x-slot name="title">Daftar</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Buat akun baru</h2>
        <p class="mt-2 text-sm text-gray-600">Daftar untuk mulai melaporkan keluhan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <input id="name"
                       name="name"
                       type="text"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       autocomplete="name"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="Masukkan nama lengkap Anda">
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

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
                       autocomplete="username"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="nama@email.com">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- RT/RW Information -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="rt_number" class="block text-sm font-medium leading-6 text-gray-900">
                    RT
                </label>
                <div class="mt-2">
                    <input id="rt_number"
                           name="rt_number"
                           type="text"
                           value="{{ old('rt_number') }}"
                           maxlength="3"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="001">
                </div>
                @error('rt_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="rw_number" class="block text-sm font-medium leading-6 text-gray-900">
                    RW
                </label>
                <div class="mt-2">
                    <input id="rw_number"
                           name="rw_number"
                           type="text"
                           value="{{ old('rw_number') }}"
                           maxlength="3"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="001">
                </div>
                @error('rw_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                Nomor Telepon
            </label>
            <div class="mt-2">
                <input id="phone"
                       name="phone"
                       type="tel"
                       value="{{ old('phone') }}"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="08xxxxxxxxxx">
            </div>
            <p class="mt-1 text-sm text-gray-500">Opsional, untuk mempermudah komunikasi</p>
            @error('phone')
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
                       autocomplete="new-password"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="Minimal 8 karakter">
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <input id="password_confirmation"
                       name="password_confirmation"
                       type="password"
                       required
                       autocomplete="new-password"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="Ulangi password Anda">
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms Agreement -->
        <div class="flex items-center">
            <input id="terms"
                   name="terms"
                   type="checkbox"
                   required
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
            <label for="terms" class="ml-3 text-sm leading-6 text-gray-900">
                Saya setuju dengan
                <a href="#" class="text-indigo-600 hover:text-indigo-500">Syarat & Ketentuan</a>
                dan
                <a href="#" class="text-indigo-600 hover:text-indigo-500">Kebijakan Privasi</a>
            </label>
        </div>
        @error('terms')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Daftar Sekarang
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                   class="font-semibold text-indigo-600 hover:text-indigo-500">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
