@extends('layouts.admin')

@section('title', 'Edit Keluhan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Keluhan #{{ $complaint->id }}</h1>
                    <p class="text-gray-600 mt-1">Kelola dan perbarui data keluhan</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($complaint->status === 'in_progress') bg-blue-100 text-blue-800
                        @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($complaint->status) }}
                    </span>
                    <a href="{{ route('admin.complaints.show', $complaint) }}"
                       class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Keluhan</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul Keluhan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $complaint->title) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select id="category_id"
                                        name="category_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('category_id', $complaint->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Keluhan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Jelaskan keluhan Anda secara detail...">{{ old('description', $complaint->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prioritas
                                </label>
                                <select id="priority"
                                        name="priority"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="low" {{ old('priority', $complaint->priority) == 'low' ? 'selected' : '' }}>
                                        Rendah
                                    </option>
                                    <option value="medium" {{ old('priority', $complaint->priority) == 'medium' ? 'selected' : '' }}>
                                        Sedang
                                    </option>
                                    <option value="high" {{ old('priority', $complaint->priority) == 'high' ? 'selected' : '' }}>
                                        Tinggi
                                    </option>
                                    <option value="urgent" {{ old('priority', $complaint->priority) == 'urgent' ? 'selected' : '' }}>
                                        Mendesak
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        name="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="pending" {{ old('status', $complaint->status) == 'pending' ? 'selected' : '' }}>
                                        Menunggu
                                    </option>
                                    <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>
                                        Sedang Diproses
                                    </option>
                                    <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                    <option value="rejected" {{ old('status', $complaint->status) == 'rejected' ? 'selected' : '' }}>
                                        Ditolak
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Response -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tanggapan Admin</h2>

                        <div>
                            <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggapan/Komentar Admin
                            </label>
                            <textarea id="admin_response"
                                      name="admin_response"
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Berikan tanggapan atau update untuk keluhan ini...">{{ old('admin_response', $complaint->admin_response) }}</textarea>
                            @error('admin_response')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="estimated_resolution" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimasi Penyelesaian
                            </label>
                            <input type="date"
                                   id="estimated_resolution"
                                   name="estimated_resolution"
                                   value="{{ old('estimated_resolution', $complaint->estimated_resolution ? $complaint->estimated_resolution->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- File Attachments -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Lampiran Foto</h2>

                        <!-- Existing Images -->
                        @if($complaint->attachments->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-3">Foto Saat Ini</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach($complaint->attachments as $attachment)
                                        <div class="relative group" id="image-{{ $attachment->id }}">
                                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                                                <img src="{{ $attachment->file_url }}"
                                                     alt="Attachment"
                                                     class="w-full h-full object-cover cursor-pointer hover:opacity-75 transition-opacity"
                                                     onclick="openImageModal('{{ $attachment->file_url }}')">
                                            </div>
                                            <button type="button"
                                                    onclick="deleteExistingImage({{ $attachment->id }})"
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                            <input type="hidden" name="keep_images[]" value="{{ $attachment->id }}" id="keep-{{ $attachment->id }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add New Images -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Tambah Foto Baru (Opsional)</h3>
                            <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                <div class="space-y-2">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="text-sm text-gray-600">
                                        <label for="images" class="cursor-pointer text-blue-600 hover:text-blue-500">
                                            Klik untuk upload
                                        </label>
                                        atau drag & drop foto di sini
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB (Maksimal 5 file)</p>
                                </div>
                                <input type="file"
                                       id="images"
                                       name="images[]"
                                       multiple
                                       accept="image/*"
                                       class="hidden"
                                       onchange="handleFileSelect(this)">
                            </div>

                            <!-- Preview New Images -->
                            <div id="previewContainer" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 hidden"></div>

                            @error('images.*')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- User Information -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelapor</h2>

                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $complaint->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $complaint->user->email }}</p>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-gray-200">
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500">Tanggal Dibuat</dt>
                                        <dd class="text-sm text-gray-900">{{ $complaint->created_at->format('d M Y, H:i') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500">Terakhir Diupdate</dt>
                                        <dd class="text-sm text-gray-900">{{ $complaint->updated_at->format('d M Y, H:i') }}</dd>
                                    </div>
                                    @if($complaint->admin_response)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500">Tanggapan Terakhir</dt>
                                        <dd class="text-sm text-gray-900">{{ $complaint->updated_at->diffForHumans() }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>

                        <div class="space-y-3">
                            <button type="button"
                                    onclick="setQuickStatus('in_progress')"
                                    class="w-full bg-blue-50 text-blue-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-100 transition-colors">
                                Sedang Diproses
                            </button>
                            <button type="button"
                                    onclick="setQuickStatus('resolved')"
                                    class="w-full bg-green-50 text-green-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-green-100 transition-colors">
                                Tandai Selesai
                            </button>
                            <button type="button"
                                    onclick="setQuickResponse('Terima kasih atas laporan Anda. Kami akan segera menindaklanjuti keluhan ini.')"
                                    class="w-full bg-gray-50 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 transition-colors">
                                Template Respon
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md font-medium hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>

                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                               class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md font-medium hover:bg-gray-200 transition-colors text-center block">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Image Modal (same as show view) -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3">
        <div class="bg-white rounded-lg shadow-2xl">
            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Foto Keluhan</h3>
                    <p id="modalImageInfo" class="text-sm text-gray-600"></p>
                </div>
                <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Image Content -->
            <div class="p-4 bg-gray-50">
                <div class="text-center">
                    <img id="modalImage" src="" alt="Foto keluhan" class="max-w-full max-h-[70vh] h-auto rounded-lg shadow-md mx-auto">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedFiles = [];
    let deletedImages = [];

    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('images');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(input) {
        handleFiles(input.files);
    }

    function handleFiles(files) {
        const maxFiles = 5;
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (selectedFiles.length + files.length > maxFiles) {
            alert(`Maksimal ${maxFiles} file dapat diupload`);
            return;
        }

        Array.from(files).forEach(file => {
            if (file.size > maxSize) {
                alert(`File ${file.name} terlalu besar. Maksimal 2MB per file.`);
                return;
            }

            if (!file.type.match('image.*')) {
                alert(`File ${file.name} bukan file gambar.`);
                return;
            }

            selectedFiles.push(file);
            previewFile(file);
        });

        updateFileInput();
    }

    function previewFile(file) {
        const container = document.getElementById('previewContainer');
        container.classList.remove('hidden');

        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                    <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">
                </div>
                <button type="button"
                        onclick="removeFile('${file.name}')"
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <p class="text-xs text-gray-500 mt-1 truncate">${file.name}</p>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    }

    function removeFile(fileName) {
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);
        updateFileInput();
        updatePreview();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function updatePreview() {
        const container = document.getElementById('previewContainer');
        container.innerHTML = '';

        if (selectedFiles.length === 0) {
            container.classList.add('hidden');
        } else {
            selectedFiles.forEach(file => previewFile(file));
        }
    }

    function deleteExistingImage(attachmentId) {
        showDeleteModal('Hapus foto ini? Tindakan ini tidak dapat dibatalkan.', function() {
            document.getElementById(`image-${attachmentId}`).style.display = 'none';
            document.getElementById(`keep-${attachmentId}`).remove();

            // Add to deleted images array
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_images[]';
            input.value = attachmentId;
            document.querySelector('form').appendChild(input);
        });
    }

    // Quick actions
    function setQuickStatus(status) {
        document.getElementById('status').value = status;

        // Set appropriate response template
        const responseField = document.getElementById('admin_response');
        let template = '';

        switch(status) {
            case 'in_progress':
                template = 'Keluhan Anda sedang kami proses. Kami akan memberikan update lebih lanjut segera.';
                break;
            case 'resolved':
                template = 'Keluhan Anda telah berhasil diselesaikan. Terima kasih atas laporan yang Anda berikan.';
                break;
        }

        if (template && !responseField.value) {
            responseField.value = template;
        }
    }

    function setQuickResponse(response) {
        document.getElementById('admin_response').value = response;
    }

    // Image modal functions
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('imageModal');
        if (!modal.classList.contains('hidden') && e.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
@endpush

@endsection
