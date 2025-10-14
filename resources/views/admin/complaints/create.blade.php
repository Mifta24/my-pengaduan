@extends('layouts.admin')

@section('title', 'Tambah Keluhan Baru')

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
                    <a href="{{ route('admin.complaints.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Keluhan</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">Tambah Keluhan</span>
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
                    Tambah Keluhan Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Buat keluhan baru atas nama pengguna
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.complaints.index') }}"
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
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <form method="POST" action="{{ route('admin.complaints.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- User Selection -->
                <div>
                    <label for="user_id" class="block text-sm font-medium leading-6 text-gray-900">Pengguna</label>
                    <div class="mt-2">
                        <select id="user_id" name="user_id" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('user_id') ring-red-300 @enderror">
                            <option value="">Pilih Pengguna</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('user_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Judul Keluhan</label>
                    <div class="mt-2">
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('title') ring-red-300 @enderror">
                    </div>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Deskripsi</label>
                    <div class="mt-2">
                        <textarea id="description" name="description" rows="4" required
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('description') ring-red-300 @enderror">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">Kategori</label>
                    <div class="mt-2">
                        <select id="category_id" name="category_id" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('category_id') ring-red-300 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('category_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium leading-6 text-gray-900">Lokasi</label>
                    <div class="mt-2">
                        <input type="text" name="location" id="location" value="{{ old('location') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('location') ring-red-300 @enderror">
                    </div>
                    @error('location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2">
                        <select id="status" name="status" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('status') ring-red-300 @enderror">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Dalam Progress</option>
                            <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Report Date -->
                <div>
                    <label for="report_date" class="block text-sm font-medium leading-6 text-gray-900">Tanggal Laporan</label>
                    <div class="mt-2">
                        <input type="date" name="report_date" id="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('report_date') ring-red-300 @enderror">
                    </div>
                    @error('report_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo -->
                <div>
                    <label for="photo" class="block text-sm font-medium leading-6 text-gray-900">Foto Utama</label>
                    <div class="mt-2">
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 @error('photo') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                    </div>
                    @error('photo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Attachments -->
                <div>
                    <label for="attachments" class="block text-sm font-medium leading-6 text-gray-900">Lampiran Tambahan</label>
                    <div class="mt-2">
                        <input type="file" name="attachments[]" id="attachments" multiple
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-indigo-500 @error('attachments.*') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Berbagai format file hingga 10MB per file</p>
                    </div>
                    @error('attachments.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end gap-x-6">
                <a href="{{ route('admin.complaints.index') }}"
                   class="text-sm font-semibold leading-6 text-gray-900">Batal</a>
                <button type="submit"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Simpan Keluhan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript for form enhancements here
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-format date input
        const dateInput = document.getElementById('report_date');
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
    });
</script>
@endpush
