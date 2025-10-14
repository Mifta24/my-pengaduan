@extends('layouts.admin')

@section('title', 'Edit Pengumuman - ' . $announcement->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
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
                        <a href="{{ route('admin.announcements.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Pengumuman</a>
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

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Edit Pengumuman
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Edit pengumuman: {{ $announcement->title }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.announcements.show', $announcement) }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Lihat
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" x-data="announcementForm()">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Dasar</h2>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium leading-6 text-gray-900">
                            Judul Pengumuman <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <input type="text" name="title" id="title"
                                   value="{{ old('title', $announcement->title) }}"
                                   required
                                   x-model="title"
                                   @input="generateSlug"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                   placeholder="Masukkan judul pengumuman...">
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium leading-6 text-gray-900">
                            Slug URL <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', $announcement->slug) }}"
                                   required
                                   x-model="slug"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                   placeholder="slug-url-pengumuman">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">URL akan menjadi: {{ url('/announcements') }}/<span x-text="slug || 'slug-url'"></span></p>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Summary -->
                    <div>
                        <label for="summary" class="block text-sm font-medium leading-6 text-gray-900">
                            Ringkasan
                        </label>
                        <div class="mt-2">
                            <textarea name="summary" id="summary" rows="3"
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      placeholder="Ringkasan singkat pengumuman...">{{ old('summary', $announcement->summary) }}</textarea>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Ringkasan akan ditampilkan di daftar pengumuman</p>
                        @error('summary')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Konten Pengumuman</h2>
                </div>
                <div class="px-6 py-6">
                    <div>
                        <label for="content" class="block text-sm font-medium leading-6 text-gray-900">
                            Isi Pengumuman <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <textarea name="content" id="content" rows="12"
                                      required
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                      placeholder="Tulis isi pengumuman lengkap di sini...">{{ old('content', $announcement->content) }}</textarea>
                        </div>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Pengaturan</h2>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium leading-6 text-gray-900">
                                Prioritas <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2">
                                <select name="priority" id="priority" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="">Pilih Prioritas</option>
                                    <option value="low" {{ old('priority', $announcement->priority) === 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('priority', $announcement->priority) === 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('priority', $announcement->priority) === 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="urgent" {{ old('priority', $announcement->priority) === 'urgent' ? 'selected' : '' }}>Mendesak</option>
                                </select>
                            </div>
                            @error('priority')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium leading-6 text-gray-900">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2">
                                <select name="status" id="status" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="">Pilih Status</option>
                                    <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Terbit</option>
                                    <option value="scheduled" {{ old('status', $announcement->status) === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                    <option value="archived" {{ old('status', $announcement->status) === 'archived' ? 'selected' : '' }}>Arsip</option>
                                </select>
                            </div>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Scheduled Publishing -->
                    <div x-show="$el.querySelector('select[name=status]').value === 'scheduled'" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium leading-6 text-gray-900">
                                Tanggal Terbit
                            </label>
                            <div class="mt-2">
                                <input type="date" name="scheduled_date" id="scheduled_date"
                                       value="{{ old('scheduled_date', $announcement->scheduled_at ? $announcement->scheduled_at->format('Y-m-d') : '') }}"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="scheduled_time" class="block text-sm font-medium leading-6 text-gray-900">
                                Waktu Terbit
                            </label>
                            <div class="mt-2">
                                <input type="time" name="scheduled_time" id="scheduled_time"
                                       value="{{ old('scheduled_time', $announcement->scheduled_at ? $announcement->scheduled_at->format('H:i') : '') }}"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>

                    <!-- Target Audience -->
                    <div>
                        <label class="text-sm font-medium leading-6 text-gray-900">Target Audience</label>
                        <div class="mt-4 space-y-4">
                            @php
                                $targetAudience = old('target_audience', $announcement->target_audience ? json_decode($announcement->target_audience, true) : []);
                            @endphp
                            <div class="flex items-center">
                                <input id="target_all" name="target_audience[]" type="checkbox" value="all"
                                       {{ in_array('all', $targetAudience) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="target_all" class="ml-3 text-sm leading-6 text-gray-600">Semua Warga</label>
                            </div>
                            <div class="flex items-center">
                                <input id="target_rt" name="target_audience[]" type="checkbox" value="rt"
                                       {{ in_array('rt', $targetAudience) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="target_rt" class="ml-3 text-sm leading-6 text-gray-600">Warga RT</label>
                            </div>
                            <div class="flex items-center">
                                <input id="target_rw" name="target_audience[]" type="checkbox" value="rw"
                                       {{ in_array('rw', $targetAudience) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="target_rw" class="ml-3 text-sm leading-6 text-gray-600">Warga RW</label>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input id="send_notification" name="send_notification" type="checkbox"
                                   {{ old('send_notification', $announcement->send_notification) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="send_notification" class="ml-3 text-sm leading-6 text-gray-600">Kirim Notifikasi</label>
                        </div>
                        <div class="flex items-center">
                            <input id="is_sticky" name="is_sticky" type="checkbox" value="1"
                                   {{ old('is_sticky', $announcement->is_sticky) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="is_sticky" class="ml-3 text-sm leading-6 text-gray-600">Sematkan di Atas</label>
                        </div>
                        <div class="flex items-center">
                            <input id="allow_comments" name="allow_comments" type="checkbox" value="1"
                                   {{ old('allow_comments', $announcement->allow_comments) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="allow_comments" class="ml-3 text-sm leading-6 text-gray-600">Izinkan Komentar</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Attachments -->
            @if($announcement->attachments && $announcement->attachments->count() > 0)
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Lampiran Saat Ini</h2>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($announcement->attachments as $attachment)
                        <div class="relative group bg-gray-50 rounded-lg p-4 hover:bg-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if(str_starts_with($attachment->mime_type, 'image/'))
                                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->file_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $this->formatFileSize($attachment->file_size) }}</p>
                                </div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 rounded-lg transition-opacity">
                                <div class="flex space-x-2">
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                       class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                                        Lihat
                                    </a>
                                    <button type="button" onclick="deleteAttachment({{ $attachment->id }})"
                                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- New Attachments -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Tambah Lampiran Baru</h2>
                </div>
                <div class="px-6 py-6">
                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10" x-data="fileUpload()">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                            </svg>
                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                <label for="attachments" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                    <span>Upload file</span>
                                    <input id="attachments" name="attachments[]" type="file" multiple class="sr-only"
                                           accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                                           @change="handleFiles">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, PDF, DOC hingga 10MB</p>

                            <!-- File preview -->
                            <div x-show="files.length > 0" class="mt-4">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md mb-2">
                                        <span class="text-sm text-gray-700" x-text="file.name"></span>
                                        <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.announcements.index') }}"
               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" name="action" value="save_draft"
                    class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                Simpan Draft
            </button>
            <button type="submit" name="action" value="update"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Update Pengumuman
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function announcementForm() {
        return {
            title: '{{ old('title', $announcement->title) }}',
            slug: '{{ old('slug', $announcement->slug) }}',

            generateSlug() {
                this.slug = this.title
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
            }
        }
    }

    function fileUpload() {
        return {
            files: [],

            handleFiles(event) {
                this.files = Array.from(event.target.files);
            },

            removeFile(index) {
                this.files.splice(index, 1);
                // Update the file input
                const dt = new DataTransfer();
                this.files.forEach(file => dt.items.add(file));
                document.getElementById('attachments').files = dt.files;
            }
        }
    }

    function deleteAttachment(attachmentId) {
        if (confirm('Apakah Anda yakin ingin menghapus lampiran ini?')) {
            fetch(`{{ route('admin.attachments.delete', '') }}/${attachmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus lampiran');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    }

    // Auto-hide scheduled fields
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.querySelector('select[name="status"]');
        const scheduledSection = statusSelect.closest('form').querySelector('[x-show]');

        function toggleScheduledFields() {
            const isScheduled = statusSelect.value === 'scheduled';
            if (scheduledSection) {
                scheduledSection.style.display = isScheduled ? 'block' : 'none';
            }
        }

        statusSelect.addEventListener('change', toggleScheduledFields);
        toggleScheduledFields(); // Initial check
    });
</script>
@endpush
@endsection
