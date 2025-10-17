@extends('layouts.admin')

@section('title', 'Buat Pengumuman Baru')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <div class="flex">
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-sm font-medium text-gray-500 hover:text-gray-700">Dashboard</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('admin.announcements.index') }}"
                                class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Pengumuman</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">Buat Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Buat Pengumuman Baru
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Buat pengumuman baru untuk warga Lurah/RW
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data"
            x-data="announcementForm()">
            @csrf

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
                                <input type="text" name="title" id="title" x-model="title"
                                    value="{{ old('title') }}" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Masukkan judul pengumuman">
                            </div>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug (Auto-generated) -->
                        <div>
                            <label for="slug" class="block text-sm font-medium leading-6 text-gray-900">
                                Slug URL <span class="text-gray-400">(otomatis dibuat)</span>
                            </label>
                            <div class="mt-2">
                                <input type="text" name="slug" id="slug" x-model="slug" readonly
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 bg-gray-50 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
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
                                    placeholder="Tulis ringkasan singkat pengumuman (opsional)">{{ old('summary') }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Ringkasan akan ditampilkan di daftar pengumuman dan
                                pratinjau</p>
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
                                <textarea name="content" id="content" rows="12" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Tulis isi pengumuman lengkap...">{{ old('content') }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Gunakan format Markdown untuk pemformatan teks.
                                <a href="#" class="text-indigo-600 hover:text-indigo-500"
                                    onclick="toggleMarkdownHelp()">Lihat panduan Markdown</a>
                            </p>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Markdown Help (Hidden by default) -->
                        <div id="markdownHelp" class="hidden mt-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Panduan Markdown:</h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <div><code>**tebal**</code> untuk <strong>teks tebal</strong></div>
                                <div><code>*miring*</code> untuk <em>teks miring</em></div>
                                <div><code># Judul</code> untuk judul besar</div>
                                <div><code>## Subjudul</code> untuk subjudul</div>
                                <div><code>- Item</code> untuk daftar</div>
                                <div><code>[Link](http://example.com)</code> untuk link</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Pengaturan</h2>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <!-- Priority and Status -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="priority" class="block text-sm font-medium leading-6 text-gray-900">
                                    Prioritas <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <select name="priority" id="priority" required
                                        class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">Pilih Prioritas</option>
                                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Rendah
                                        </option>
                                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Sedang
                                        </option>
                                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Tinggi
                                        </option>
                                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                            Mendesak</option>
                                    </select>
                                </div>
                                @error('priority')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium leading-6 text-gray-900">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <select name="status" id="status" required x-model="status"
                                        class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">Pilih Status</option>
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>
                                            Publikasikan Sekarang</option>
                                        <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>
                                            Jadwalkan</option>
                                    </select>
                                </div>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Scheduled Publication -->
                        <div x-show="status === 'scheduled'" x-transition class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="scheduled_date" class="block text-sm font-medium leading-6 text-gray-900">
                                    Tanggal Publikasi <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <input type="date" name="scheduled_date" id="scheduled_date"
                                        value="{{ old('scheduled_date') }}" min="{{ date('Y-m-d') }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                @error('scheduled_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="scheduled_time" class="block text-sm font-medium leading-6 text-gray-900">
                                    Waktu Publikasi <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <input type="time" name="scheduled_time" id="scheduled_time"
                                        value="{{ old('scheduled_time', '09:00') }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                @error('scheduled_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Target Audience -->
                        <div>
                            <fieldset>
                                <legend class="text-sm font-medium leading-6 text-gray-900">
                                    Target Audiens <span class="text-red-500">*</span>
                                </legend>
                                <div class="mt-4 space-y-4">
                                    <div class="flex items-center">
                                        <input id="target_all" name="target_audience[]" type="checkbox" value="all"
                                            {{ in_array('all', old('target_audience', [])) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                        <label for="target_all" class="ml-3 text-sm leading-6 text-gray-900">
                                            Semua Warga
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="target_rt" name="target_audience[]" type="checkbox" value="pengurus_rt"
                                            {{ in_array('pengurus_rt', old('target_audience', [])) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                        <label for="target_rt" class="ml-3 text-sm leading-6 text-gray-900">
                                            Pengurus Lurah
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                            @error('target_audience')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Options -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="send_notification" name="send_notification" type="checkbox"
                                    {{ old('send_notification', true) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="send_notification" class="ml-3 text-sm leading-6 text-gray-900">
                                    Kirim notifikasi email ke target audiens
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="is_sticky" name="is_sticky" type="checkbox" value="1"
                                    {{ old('is_sticky') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="is_sticky" class="ml-3 text-sm leading-6 text-gray-900">
                                    Tempel di atas (pengumuman penting)
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="allow_comments" name="allow_comments" type="checkbox" value="1"
                                    {{ old('allow_comments', true) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="allow_comments" class="ml-3 text-sm leading-6 text-gray-900">
                                    Izinkan komentar warga
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Attachments -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Lampiran</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div>
                            <label for="attachments" class="block text-sm font-medium leading-6 text-gray-900">
                                Upload File
                            </label>
                            <div
                                class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.061l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                        <label for="attachments"
                                            class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                            <span>Upload file</span>
                                            <input id="attachments" name="attachments[]" type="file" multiple
                                                class="sr-only" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" onchange="previewAttachments(this)">
                                        </label>
                                        <p class="pl-1">atau drag dan drop</p>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-600">PDF, DOC, DOCX, JPG, PNG, GIF hingga 10MB
                                        per file</p>
                                </div>
                            </div>

                            <!-- Preview Attachments -->
                            <div id="attachments-preview" class="mt-4 hidden"></div>

                            <p class="mt-2 text-sm text-gray-500">Upload dokumen pendukung atau gambar untuk pengumuman</p>
                            @error('attachments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('attachments.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.announcements.index') }}"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Batal
                    </a>

                    <div class="flex space-x-3">
                        <button type="submit" name="action" value="save_draft"
                            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                            </svg>
                            Simpan Draft
                        </button>

                        <button type="submit" name="action" value="publish"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Publikasikan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            function announcementForm() {
                return {
                    title: '',
                    slug: '',
                    status: 'draft',

                    init() {
                        this.$watch('title', (value) => {
                            this.slug = this.createSlug(value);
                        });
                    },

                    createSlug(text) {
                        return text
                            .toLowerCase()
                            .replace(/[^\w\s-]/g, '') // Remove special characters
                            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
                    }
                }
            }

            function toggleMarkdownHelp() {
                const help = document.getElementById('markdownHelp');
                help.classList.toggle('hidden');
            }

            // File upload preview
            let attachmentFiles = [];

            function previewAttachments(input) {
                const files = Array.from(input.files);
                const maxFiles = 10;
                const maxSize = 10 * 1024 * 1024; // 10MB
                const container = document.getElementById('attachments-preview');

                // Don't clear existing files, add to them
                files.forEach(file => {
                    if (file.size > maxSize) {
                        alert(`File ${file.name} terlalu besar. Maksimal 10MB per file.`);
                        return;
                    }
                    attachmentFiles.push(file);
                });

                if (attachmentFiles.length > maxFiles) {
                    alert(`Maksimal ${maxFiles} file dapat diupload`);
                    attachmentFiles = attachmentFiles.slice(0, maxFiles);
                }

                // Update the file input with current files
                updateAttachmentFileInput();

                // Re-render preview
                renderAttachmentPreview();
            }

            function renderAttachmentPreview() {
                const container = document.getElementById('attachments-preview');
                container.innerHTML = '';

                if (attachmentFiles.length === 0) {
                    container.classList.add('hidden');
                    container.classList.remove('grid', 'grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'gap-4');
                    return;
                }

                container.classList.remove('hidden');
                container.classList.add('grid', 'grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'gap-4');

                attachmentFiles.forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'relative group';

                    // Check if file is image
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            div.innerHTML = `
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200">
                                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover">
                                </div>
                                <button type="button"
                                        onclick="removeAttachment(${index})"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // For non-image files, show icon
                        const icon = getFileIcon(file.type);
                        div.innerHTML = `
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200 flex items-center justify-center">
                                ${icon}
                            </div>
                            <button type="button"
                                    onclick="removeAttachment(${index})"
                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                        `;
                    }

                    container.appendChild(div);
                });
            }

            function getFileIcon(fileType) {
                if (fileType.includes('pdf')) {
                    return `<svg class="w-16 h-16 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.5,16H14V19H12.5V16H11V14.5H12.5V13.5C12.5,12.7 13.2,12 14,12H15.5V13.5H14V14.5H15.5V16Z"/>
                    </svg>`;
                } else if (fileType.includes('word') || fileType.includes('document')) {
                    return `<svg class="w-16 h-16 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.2,20H13.8L12,13.2L10.2,20H8.8L6.6,11H8.1L9.5,17.8L11.3,11H12.6L14.4,17.8L15.8,11H17.3L15.2,20M13,9V3.5L18.5,9H13Z"/>
                    </svg>`;
                } else {
                    return `<svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>`;
                }
            }

            function removeAttachment(index) {
                attachmentFiles.splice(index, 1);
                updateAttachmentFileInput();
                renderAttachmentPreview();
            }

            function updateAttachmentFileInput() {
                const dt = new DataTransfer();
                attachmentFiles.forEach(file => dt.items.add(file));
                document.getElementById('attachments').files = dt.files;
            }
        </script>
    @endpush
@endsection
