<x-guest-layout>
    <x-slot name="title">Daftar</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Buat akun baru</h2>
        <p class="mt-2 text-sm text-gray-600">Daftar untuk mulai melaporkan keluhan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6" enctype="multipart/form-data">
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

        <!-- NIK -->
        <div>
            <label for="nik" class="block text-sm font-medium leading-6 text-gray-900">
                NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <input id="nik"
                       name="nik"
                       type="text"
                       value="{{ old('nik') }}"
                       required
                       maxlength="16"
                       pattern="[0-9]{16}"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       placeholder="Masukkan 16 digit NIK">
            </div>
            <p class="mt-1 text-sm text-gray-500">NIK harus 16 digit angka sesuai KTP</p>
            @error('nik')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- KTP Upload -->
        <div>
            <label for="ktp_file" class="block text-sm font-medium leading-6 text-gray-900">
                Upload Foto KTP <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <div class="flex items-center justify-center w-full">
                    <label for="ktp_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-3 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                            <p class="text-xs text-gray-500">JPG, JPEG atau PNG (MAX. 2MB)</p>
                        </div>
                        <input id="ktp_file" name="ktp_file" type="file" accept="image/jpeg,image/jpg,image/png" required class="hidden">
                    </label>
                </div>
                <div id="ktp-preview" class="mt-3 hidden">
                    <img id="ktp-image" class="rounded-lg max-h-48 mx-auto" alt="Preview KTP">
                    <p class="text-center text-sm text-gray-600 mt-2" id="ktp-filename"></p>
                </div>
            </div>
            <p class="mt-1 text-sm text-gray-500">Upload foto KTP untuk verifikasi oleh admin</p>
            @error('ktp_file')
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
                    RT (Rukun Tetangga)
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
                    RW (Rukun Warga)
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

        <!-- Address -->
        <div>
            <label for="address" class="block text-sm font-medium leading-6 text-gray-900">
                Alamat Lengkap <span class="text-red-500">*</span>
            </label>
            <div class="mt-2">
                <textarea id="address"
                          name="address"
                          rows="3"
                          required
                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          placeholder="Masukkan alamat lengkap sesuai KTP">{{ old('address') }}</textarea>
            </div>
            <p class="mt-1 text-sm text-gray-500">Alamat harus sesuai dengan yang tertera di KTP</p>
            @error('address')
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

    @push('scripts')
    <script>
        // KTP Preview Handler
        document.getElementById('ktp_file').addEventListener('change', function(e) {
            const preview = document.getElementById('ktp-preview');
            const image = document.getElementById('ktp-image');
            const filename = document.getElementById('ktp-filename');

            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    image.src = e.target.result;
                    filename.textContent = this.files[0].name;
                    preview.classList.remove('hidden');
                }.bind(this);

                reader.readAsDataURL(this.files[0]);
            }
        });

        // Validate NIK input (numbers only)
        document.getElementById('nik').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
    @endpush
</x-guest-layout>
