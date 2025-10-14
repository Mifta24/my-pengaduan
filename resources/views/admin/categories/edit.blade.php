@extends('layouts.admin')

@section('title', 'Edit Kategori - ' . $category->name)

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
                    <span class="ml-4 text-sm font-medium text-gray-500">Edit</span>
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
                    Edit Kategori
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Edit kategori: {{ $category->name }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.categories.show', $category) }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Lihat
                </a>
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

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Keluhan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->where('status', 'resolved')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->where('status', 'pending')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
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
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('slug') ring-red-500 @enderror"
                               placeholder="infrastruktur-jalan">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">URL-friendly version dari nama kategori</p>
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
                                  placeholder="Deskripsi singkat tentang kategori ini...">{{ old('description', $category->description) }}</textarea>
                    </div>
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
                            <option value="">Pilih Icon</option>
                            <option value="building" {{ old('icon', $category->icon) === 'building' ? 'selected' : '' }}>🏢 Building</option>
                            <option value="road" {{ old('icon', $category->icon) === 'road' ? 'selected' : '' }}>🛣️ Road</option>
                            <option value="trash" {{ old('icon', $category->icon) === 'trash' ? 'selected' : '' }}>🗑️ Trash</option>
                            <option value="security" {{ old('icon', $category->icon) === 'security' ? 'selected' : '' }}>🛡️ Security</option>
                            <option value="water" {{ old('icon', $category->icon) === 'water' ? 'selected' : '' }}>💧 Water</option>
                            <option value="electric" {{ old('icon', $category->icon) === 'electric' ? 'selected' : '' }}>⚡ Electric</option>
                            <option value="health" {{ old('icon', $category->icon) === 'health' ? 'selected' : '' }}>🏥 Health</option>
                            <option value="education" {{ old('icon', $category->icon) === 'education' ? 'selected' : '' }}>🎓 Education</option>
                            <option value="transport" {{ old('icon', $category->icon) === 'transport' ? 'selected' : '' }}>🚌 Transport</option>
                            <option value="park" {{ old('icon', $category->icon) === 'park' ? 'selected' : '' }}>🌳 Park</option>
                            <option value="sport" {{ old('icon', $category->icon) === 'sport' ? 'selected' : '' }}>⚽ Sport</option>
                            <option value="other" {{ old('icon', $category->icon) === 'other' ? 'selected' : '' }}>📝 Other</option>
                        </select>
                    </div>
                    @error('icon')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color -->
                <div>
                    <label for="color" class="block text-sm font-medium leading-6 text-gray-900">
                        Warna Theme
                    </label>
                    <div class="mt-2">
                        <select name="color" id="color"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('color') ring-red-500 @enderror">
                            <option value="">Pilih Warna</option>
                            <option value="blue" {{ old('color', $category->color) === 'blue' ? 'selected' : '' }}>🔵 Blue</option>
                            <option value="green" {{ old('color', $category->color) === 'green' ? 'selected' : '' }}>🟢 Green</option>
                            <option value="yellow" {{ old('color', $category->color) === 'yellow' ? 'selected' : '' }}>🟡 Yellow</option>
                            <option value="red" {{ old('color', $category->color) === 'red' ? 'selected' : '' }}>🔴 Red</option>
                            <option value="purple" {{ old('color', $category->color) === 'purple' ? 'selected' : '' }}>🟣 Purple</option>
                            <option value="indigo" {{ old('color', $category->color) === 'indigo' ? 'selected' : '' }}>🔵 Indigo</option>
                            <option value="pink" {{ old('color', $category->color) === 'pink' ? 'selected' : '' }}>🩷 Pink</option>
                            <option value="gray" {{ old('color', $category->color) === 'gray' ? 'selected' : '' }}>⚪ Gray</option>
                        </select>
                    </div>
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
                            <option value="1" {{ old('is_active', $category->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $category->is_active) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Kategori yang tidak aktif tidak akan tampil di form keluhan</p>
                    @error('is_active')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.categories.index') }}"
                       class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Update Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        document.getElementById('slug').value = slug;
    });
</script>
@endpush
@endsection
