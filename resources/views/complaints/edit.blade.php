@extends('layouts.app')

@section('title', 'Edit Keluhan - Sistem Pengaduan Lurah/RW')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="border-b border-gray-200 pb-5 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Edit Keluhan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Ubah detail keluhan Anda. Hanya keluhan dengan status "Menunggu" yang dapat diubah.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('complaints.show', $complaint) }}"
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
        <form action="{{ route('complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">
                    Judul Keluhan <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input type="text" name="title" id="title" value="{{ old('title', $complaint->title) }}" required
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('title') ring-red-500 @enderror"
                           placeholder="Masukkan judul keluhan yang jelas dan singkat">
                </div>
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <select name="category_id" id="category_id" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('category_id') ring-red-500 @enderror">
                        <option value="">Pilih kategori keluhan</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $complaint->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('category_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                    Deskripsi Keluhan <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <textarea name="description" id="description" rows="6" required
                              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('description') ring-red-500 @enderror"
                              placeholder="Jelaskan keluhan Anda secara detail...">{{ old('description', $complaint->description) }}</textarea>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    Berikan penjelasan yang detail dan jelas agar keluhan Anda dapat ditangani dengan baik.
                </p>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium leading-6 text-gray-900">
                    Lokasi Kejadian
                </label>
                <div class="mt-2">
                    <input type="text" name="location" id="location" value="{{ old('location', $complaint->location) }}"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('location') ring-red-500 @enderror"
                           placeholder="Contoh: Jalan Mawar No. 15, Lurah 001/RW 002">
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    Sebutkan lokasi spesifik dimana masalah ini terjadi (opsional).
                </p>
                @error('location')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Priority -->
            <div>
                <label for="priority" class="block text-sm font-medium leading-6 text-gray-900">
                    Prioritas <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <select name="priority" id="priority" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('priority') ring-red-500 @enderror">
                        <option value="">Pilih tingkat prioritas</option>
                        <option value="low" {{ old('priority', $complaint->priority) === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ old('priority', $complaint->priority) === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ old('priority', $complaint->priority) === 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="urgent" {{ old('priority', $complaint->priority) === 'urgent' ? 'selected' : '' }}>Mendesak</option>
                    </select>
                </div>
                @error('priority')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Existing Images -->
            @if($complaint->attachments->count() > 0)
                <div>
                    <label class="block text-sm font-medium leading-6 text-gray-900 mb-3">
                        Foto Saat Ini
                    </label>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 mb-4">
                        @foreach($complaint->attachments as $attachment)
                            <div class="relative group">
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                                    <img src="{{ $attachment->file_url }}"
                                         alt="Foto keluhan"
                                         class="w-full h-full object-cover">
                                </div>
                                <button type="button"
                                        onclick="removeExistingImage({{ $attachment->id }})"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                    ×
                                </button>
                                <p class="mt-1 text-xs text-gray-600 truncate">{{ $attachment->file_name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- New Images -->
            <div>
                <label for="images" class="block text-sm font-medium leading-6 text-gray-900">
                    Tambah Foto Baru
                </label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10" id="dropzone">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                        </svg>
                        <div class="mt-4 flex text-sm leading-6 text-gray-600">
                            <label for="images" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                <span>Upload foto baru</span>
                                <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/jpeg,image/png,image/jpg,image/gif" max="5">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF hingga 10MB (maksimal 5 file total)</p>
                    </div>
                </div>

                <!-- New Image Preview -->
                <div id="imagePreview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5" style="display: none;">
                    <!-- Preview images will be added here -->
                </div>

                @error('images')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hidden inputs for deleted images -->
            <div id="deletedImages"></div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('complaints.show', $complaint) }}"
                   class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    const dropzone = document.getElementById('dropzone');
    let selectedFiles = [];
    let deletedImageIds = [];

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Drag and drop handlers
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropzone.classList.add('border-indigo-500', 'bg-indigo-50');
    });

    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');

        const files = Array.from(e.dataTransfer.files).filter(file =>
            file.type.startsWith('image/')
        );

        if (files.length > 0) {
            handleFiles(files);
        }
    });

    function handleFiles(files) {
        const newFiles = Array.from(files).slice(0, 5 - selectedFiles.length);

        if (selectedFiles.length + newFiles.length > 5) {
            alert('Maksimal 5 file yang dapat diupload.');
            return;
        }

        newFiles.forEach(file => {
            if (file.size > 10 * 1024 * 1024) {
                alert(`File ${file.name} terlalu besar. Maksimal 10MB.`);
                return;
            }

            if (!file.type.match(/image\/(jpeg|jpg|png|gif)/)) {
                alert(`File ${file.name} bukan format yang didukung.`);
                return;
            }

            selectedFiles.push(file);
            createPreview(file, selectedFiles.length - 1);
        });

        updateFileInput();
        updatePreviewVisibility();
    }

    function createPreview(file, index) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'relative group';
            previewItem.innerHTML = `
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                    <img src="${e.target.result}"
                         alt="Preview ${index + 1}"
                         class="w-full h-full object-cover">
                </div>
                <button type="button"
                        onclick="removeNewImage(${index})"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                    ×
                </button>
                <p class="mt-1 text-xs text-gray-600 truncate">${file.name}</p>
            `;

            imagePreview.appendChild(previewItem);
        };

        reader.readAsDataURL(file);
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function updatePreviewVisibility() {
        imagePreview.style.display = selectedFiles.length > 0 ? 'grid' : 'none';
    }

    // Global functions
    window.removeNewImage = function(index) {
        selectedFiles.splice(index, 1);
        imagePreview.innerHTML = '';

        selectedFiles.forEach((file, newIndex) => {
            createPreview(file, newIndex);
        });

        updateFileInput();
        updatePreviewVisibility();
    };

    window.removeExistingImage = function(attachmentId) {
        if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
            deletedImageIds.push(attachmentId);

            // Hide the image element
            event.target.closest('.relative').style.display = 'none';

            // Add hidden input for deleted image
            const deletedImagesContainer = document.getElementById('deletedImages');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'deleted_images[]';
            hiddenInput.value = attachmentId;
            deletedImagesContainer.appendChild(hiddenInput);
        }
    };
});
</script>
@endpush
@endsection
