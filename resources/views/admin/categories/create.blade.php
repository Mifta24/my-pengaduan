@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div class="flex">
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Dashboard</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <a href="{{ route('admin.categories.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Kategori</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">Tambah</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Tambah Kategori
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Buat kategori baru untuk pengelompokan keluhan.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6 p-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('name') ring-red-500 @enderror"
                               placeholder="Contoh: Infrastruktur, Kebersihan, Keamanan">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium leading-6 text-gray-900">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('slug') ring-red-500 @enderror"
                               placeholder="infrastruktur, kebersihan, keamanan">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        URL-friendly version dari nama kategori. Akan dibuat otomatis jika dikosongkan.
                    </p>
                    @error('slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                        Deskripsi
                    </label>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="4"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('description') ring-red-500 @enderror"
                                  placeholder="Jelaskan kategori ini untuk membantu warga memilih kategori yang tepat...">{{ old('description') }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Deskripsi ini akan membantu warga memahami jenis keluhan yang masuk dalam kategori ini.
                    </p>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium leading-6 text-gray-900">
                        Icon
                    </label>
                    <div class="mt-2">
                        <select name="icon" id="icon"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('icon') ring-red-500 @enderror">
                            <option value="">Pilih icon (opsional)</option>
                            <option value="🏗️" {{ old('icon') === '🏗️' ? 'selected' : '' }}>🏗️ Infrastruktur</option>
                            <option value="🧹" {{ old('icon') === '🧹' ? 'selected' : '' }}>🧹 Kebersihan</option>
                            <option value="🔒" {{ old('icon') === '🔒' ? 'selected' : '' }}>🔒 Keamanan</option>
                            <option value="💡" {{ old('icon') === '💡' ? 'selected' : '' }}>💡 Listrik</option>
                            <option value="💧" {{ old('icon') === '💧' ? 'selected' : '' }}>💧 Air</option>
                            <option value="🚗" {{ old('icon') === '🚗' ? 'selected' : '' }}>🚗 Parkir</option>
                            <option value="🌳" {{ old('icon') === '🌳' ? 'selected' : '' }}>🌳 Lingkungan</option>
                            <option value="🏥" {{ old('icon') === '🏥' ? 'selected' : '' }}>🏥 Kesehatan</option>
                            <option value="📢" {{ old('icon') === '📢' ? 'selected' : '' }}>📢 Informasi</option>
                            <option value="⚡" {{ old('icon') === '⚡' ? 'selected' : '' }}>⚡ Darurat</option>
                            <option value="📝" {{ old('icon') === '📝' ? 'selected' : '' }}>📝 Administrasi</option>
                            <option value="🎉" {{ old('icon') === '🎉' ? 'selected' : '' }}>🎉 Acara</option>
                        </select>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Icon akan ditampilkan di daftar kategori untuk memudahkan identifikasi.
                    </p>
                    @error('icon')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color -->
                <div>
                    <label for="color" class="block text-sm font-medium leading-6 text-gray-900">
                        Warna
                    </label>
                    <div class="mt-2">
                        <select name="color" id="color"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('color') ring-red-500 @enderror">
                            <option value="">Pilih warna (opsional)</option>
                            <option value="#3B82F6" {{ old('color') === '#3B82F6' ? 'selected' : '' }} style="background-color: #3B82F6; color: white;">🔵 Biru</option>
                            <option value="#EF4444" {{ old('color') === '#EF4444' ? 'selected' : '' }} style="background-color: #EF4444; color: white;">🔴 Merah</option>
                            <option value="#10B981" {{ old('color') === '#10B981' ? 'selected' : '' }} style="background-color: #10B981; color: white;">🟢 Hijau</option>
                            <option value="#F59E0B" {{ old('color') === '#F59E0B' ? 'selected' : '' }} style="background-color: #F59E0B; color: white;">🟡 Kuning</option>
                            <option value="#8B5CF6" {{ old('color') === '#8B5CF6' ? 'selected' : '' }} style="background-color: #8B5CF6; color: white;">🟣 Ungu</option>
                            <option value="#EC4899" {{ old('color') === '#EC4899' ? 'selected' : '' }} style="background-color: #EC4899; color: white;">🌸 Pink</option>
                            <option value="#06B6D4" {{ old('color') === '#06B6D4' ? 'selected' : '' }} style="background-color: #06B6D4; color: white;">🟦 Cyan</option>
                            <option value="#84CC16" {{ old('color') === '#84CC16' ? 'selected' : '' }} style="background-color: #84CC16; color: white;">🟩 Lime</option>
                        </select>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Warna akan digunakan untuk membedakan kategori dalam tampilan grafik dan statistik.
                    </p>
                    @error('color')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium leading-6 text-gray-900">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <select name="is_active" id="is_active" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('is_active') ring-red-500 @enderror">
                            <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Hanya kategori aktif yang akan ditampilkan kepada warga saat mengajukan keluhan.
                    </p>
                    @error('is_active')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.categories.index') }}"
                       class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Buat Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function(e) {
        const name = e.target.value;
        const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens

        document.getElementById('slug').value = slug;
    });
</script>
@endpush
@endsection
