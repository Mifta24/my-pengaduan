@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Profil
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Kelola informasi profil dan pengaturan akun Anda
        </p>
    </div>

    <!-- Success Messages -->
    @if (session('status') === 'profile-updated')
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Profil berhasil diperbarui!</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('status') === 'notifications-updated')
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Preferensi notifikasi berhasil diperbarui!</p>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-8">
        <!-- Profile Information -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Profil</h2>
                <p class="mt-1 text-sm text-gray-600">Update informasi profil dan alamat email Anda.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="px-6 py-6">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Avatar -->
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            @if($user->avatar)
                                <img class="h-16 w-16 object-cover rounded-full"
                                     src="{{ $user->avatar_url }}"
                                     alt="{{ $user->name }}">
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="avatar" class="block text-sm font-medium leading-6 text-gray-900">
                                Foto Profil
                            </label>
                            <div class="mt-2 flex items-center space-x-4">
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @if($user->avatar)
                                    <button type="submit" name="remove_avatar" value="1"
                                            class="text-sm text-red-600 hover:text-red-500">
                                        Hapus foto
                                    </button>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-600">JPG, JPEG, PNG hingga 2MB</p>
                            @error('avatar')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2">
                                <input type="text" name="name" id="name"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nik" class="block text-sm font-medium leading-6 text-gray-900">
                                NIK (Nomor Induk Kependudukan)
                            </label>
                            <div class="mt-2">
                                <input type="text" id="nik"
                                       value="{{ $user->nik ?? 'Belum diisi' }}"
                                       readonly
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 bg-gray-50 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6 cursor-not-allowed">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">NIK tidak dapat diubah setelah registrasi</p>
                        </div>
                    </div>

                    <!-- KTP Display -->
                    @if($user->ktp_path)
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900 mb-2">
                            Foto KTP yang Terdaftar
                        </label>
                        <div class="relative rounded-lg border-2 border-gray-200 p-4 bg-gray-50">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <img src="{{ $user->ktp_url }}"
                                         alt="KTP {{ $user->name }}"
                                         class="h-32 w-auto rounded-lg border border-gray-300 object-contain cursor-pointer hover:opacity-75 transition"
                                         onclick="openKTPModal()">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Terverifikasi</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        KTP Anda telah diverifikasi oleh admin. Klik gambar untuk melihat lebih besar.
                                    </p>
                                    @if($user->verified_at)
                                    <p class="mt-1 text-xs text-gray-500">
                                        Diverifikasi pada: {{ $user->verified_at->format('d M Y, H:i') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900 mb-2">
                            Foto KTP
                        </label>
                        <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 bg-gray-50">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Belum ada foto KTP yang diupload</p>
                                <p class="text-xs text-gray-500">Silakan hubungi admin untuk update KTP</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Phone and RT/RW -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                                Nomor Telepon
                            </label>
                            <div class="mt-2">
                                <input type="tel" name="phone" id="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="rt_number" class="block text-sm font-medium leading-6 text-gray-900">
                                RT (Rukun Tetangga)
                            </label>
                            <div class="mt-2">
                                <input type="text" name="rt_number" id="rt_number"
                                       value="{{ old('rt_number', $user->rt_number) }}"
                                       placeholder="001"
                                       maxlength="3"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                                <input type="text" name="rw_number" id="rw_number"
                                       value="{{ old('rw_number', $user->rw_number) }}"
                                       placeholder="001"
                                       maxlength="3"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            @error('rw_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium leading-6 text-gray-900">
                            Alamat Lengkap
                        </label>
                        <div class="mt-2">
                            <textarea name="address" id="address" rows="3"
                                     placeholder="Masukkan alamat lengkap..."
                                     class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('address', $user->address) }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Ubah Password</h2>
                <p class="mt-1 text-sm text-gray-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="px-6 py-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium leading-6 text-gray-900">
                            Password Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <input type="password" name="current_password" id="current_password"
                                   required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2">
                                <input type="password" name="password" id="password"
                                       required
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       required
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification Preferences -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Preferensi Notifikasi</h2>
                <p class="mt-1 text-sm text-gray-600">Atur bagaimana Anda ingin menerima notifikasi.</p>
            </div>

            <form method="POST" action="{{ route('profile.notifications') }}" class="px-6 py-6">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <fieldset>
                        <legend class="text-sm font-medium leading-6 text-gray-900">Notifikasi Email</legend>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <input id="complaint_updates" name="complaint_updates" type="checkbox"
                                       {{ $user->notification_preferences['complaint_updates'] ?? true ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="complaint_updates" class="ml-3 text-sm leading-6 text-gray-900">
                                    Update status keluhan
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="system_announcements" name="system_announcements" type="checkbox"
                                       {{ $user->notification_preferences['system_announcements'] ?? true ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="system_announcements" class="ml-3 text-sm leading-6 text-gray-900">
                                    Pengumuman penting
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="email_notifications" name="email_notifications" type="checkbox"
                                       {{ $user->notification_preferences['email_notifications'] ?? true ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="email_notifications" class="ml-3 text-sm leading-6 text-gray-900">
                                    Notifikasi via email
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="marketing_emails" name="marketing_emails" type="checkbox"
                                       {{ $user->notification_preferences['marketing_emails'] ?? false ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="marketing_emails" class="ml-3 text-sm leading-6 text-gray-900">
                                    Email promosi dan tips
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-sm font-medium leading-6 text-gray-900">Notifikasi Browser</legend>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <input id="push_notifications" name="push_notifications" type="checkbox"
                                       {{ $user->notification_preferences['push_notifications'] ?? false ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="push_notifications" class="ml-3 text-sm leading-6 text-gray-900">
                                    Aktifkan notifikasi browser
                                </label>
                            </div>
                            <p class="text-sm text-gray-600 ml-7">
                                Terima notifikasi langsung di browser untuk update penting
                            </p>
                        </div>
                    </fieldset>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Simpan Preferensi
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Actions -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Aksi Akun</h2>
                <p class="mt-1 text-sm text-gray-600">Kelola dan hapus akun Anda.</p>
            </div>

            <div class="px-6 py-6 space-y-4">
                <!-- Email Verification -->
                @if (!$user->hasVerifiedEmail())
                    <div class="rounded-md bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Email belum diverifikasi</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Alamat email Anda belum diverifikasi. Silakan cek email Anda untuk link verifikasi.</p>
                                </div>
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="bg-yellow-50 text-yellow-800 font-medium text-sm hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-yellow-50 rounded-md px-2 py-1">
                                            Kirim Ulang Email Verifikasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Export Data -->
                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Export Data</h3>
                        <p class="text-sm text-gray-600">Unduh semua data pribadi dan keluhan Anda.</p>
                    </div>
                    <a href="{{ route('profile.export') }}"
                       class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export Data
                    </a>
                </div>

                <!-- Delete Account -->
                <div class="flex items-center justify-between py-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Hapus Akun</h3>
                        <p class="text-sm text-gray-600">
                            Hapus akun Anda secara permanen. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <button type="button"
                            onclick="openDeleteModal()"
                            class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Hapus Akun</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus akun Anda? Semua data termasuk keluhan akan dihapus secara permanen.
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 text-left">
                        Konfirmasi dengan password Anda:
                    </label>
                    <input type="password" name="password" id="delete_password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>

                <div class="flex items-center px-4 py-3 space-x-2">
                    <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- KTP Preview Modal -->
@if($user->ktp_path)
<div id="ktpModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50" onclick="closeKTPModal()">
    <div class="relative top-10 mx-auto p-5 w-full max-w-4xl">
        <div class="relative bg-white rounded-lg shadow-xl">
            <!-- Close button -->
            <button type="button"
                    onclick="closeKTPModal()"
                    class="absolute top-4 right-4 text-gray-400 bg-white rounded-full p-2 hover:text-gray-600 hover:bg-gray-100 focus:outline-none z-10">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- KTP Image -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto KTP - {{ $user->name }}</h3>
                <img src="{{ $user->ktp_url }}"
                     alt="KTP {{ $user->name }}"
                     class="w-full h-auto rounded-lg"
                     onclick="event.stopPropagation()">
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>NIK:</strong> {{ $user->nik }}</p>
                    @if($user->verified_at)
                    <p class="text-green-600 mt-2">
                        <svg class="inline h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Diverifikasi pada: {{ $user->verified_at->format('d M Y, H:i') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('delete_password').value = '';
    }

    function openKTPModal() {
        document.getElementById('ktpModal').classList.remove('hidden');
    }

    function closeKTPModal() {
        document.getElementById('ktpModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Close KTP modal when clicking outside
    const ktpModal = document.getElementById('ktpModal');
    if (ktpModal) {
        ktpModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeKTPModal();
            }
        });
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
            if (ktpModal) {
                closeKTPModal();
            }
        }
    });

    // Request notification permission when browser notifications checkbox is checked
    document.getElementById('browser_notifications').addEventListener('change', function() {
        if (this.checked && 'Notification' in window) {
            if (Notification.permission === 'default') {
                Notification.requestPermission().then(function(permission) {
                    if (permission !== 'granted') {
                        document.getElementById('browser_notifications').checked = false;
                        alert('Izin notifikasi diperlukan untuk mengaktifkan fitur ini.');
                    }
                });
            } else if (Notification.permission === 'denied') {
                this.checked = false;
                alert('Notifikasi browser diblokir. Silakan aktifkan melalui pengaturan browser.');
            }
        }
    });
</script>
@endpush
@endsection
