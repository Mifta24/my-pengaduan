@extends('layouts.admin')

@section('title', 'Detail Keluhan - ' . $complaint->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
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
                    <span class="ml-4 text-sm font-medium text-gray-500">Detail Keluhan</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 truncate">
                        {{ $complaint->title }}
                    </h1>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span>ID: #{{ $complaint->id }}</span>
                        <span>•</span>
                        <span>{{ $complaint->created_at->format('d M Y, H:i') }}</span>
                        @if($complaint->category)
                            <span>•</span>
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                @if($complaint->category->icon)
                                    {{ $complaint->category->icon }}
                                @endif
                                {{ $complaint->category->name }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="ml-6 flex items-center space-x-3">
                    <!-- Status Update - Replace dropdown with buttons for better UX -->
                    @if($complaint->status !== 'resolved')
                    <div class="flex space-x-2">
                        @if($complaint->status === 'pending')
                        <button type="button" onclick="updateStatus('in_progress')"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                            Proses
                        </button>
                        @endif

                        @if($complaint->status === 'in_progress')
                        <button type="button" onclick="openResolveModal()"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            Selesaikan
                        </button>
                        @endif

                        @if($complaint->status !== 'rejected')
                        <button type="button" onclick="updateStatus('rejected')"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                            Tolak
                        </button>
                        @endif
                    </div>
                    @endif

                    <a href="{{ route('admin.complaints.edit', $complaint) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    <a href="{{ route('admin.complaints.index') }}"
                       class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Daftar
                    </a>
                    <button type="button" onclick="scrollToResponse()"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        Berikan Respon
                    </button>
                </div>
                <div class="text-sm text-gray-500">
                    Dilaporkan oleh: <strong>{{ $complaint->user->name }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Deskripsi Keluhan</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($complaint->description)) !!}
                    </div>
                    @if($complaint->location)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-medium text-blue-800">Lokasi:</span>
                            </div>
                            <p class="mt-1 text-sm text-blue-700">{{ $complaint->location }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Images -->
            @if($complaint->complaintAttachments && $complaint->complaintAttachments->count() > 0)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Foto Keluhan</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach($complaint->complaintAttachments as $attachment)
                                <div class="relative">
                                    <img src="{{ Storage::url($attachment->file_path) }}"
                                         alt="Foto keluhan"
                                         class="h-24 w-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                                         onclick="openImageModal('{{ Storage::url($attachment->file_path) }}')">
                                    <div class="absolute inset-0 rounded-lg ring-1 ring-inset ring-gray-900/10"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Resolution Photos -->
            @if($complaint->status === 'resolved' && $complaint->resolutionAttachments && $complaint->resolutionAttachments->count() > 0)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <svg class="w-5 h-5 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dokumentasi Penyelesaian
                        </h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach($complaint->resolutionAttachments as $attachment)
                                <div class="relative">
                                    <img src="{{ Storage::url($attachment->file_path) }}"
                                         alt="Foto penyelesaian"
                                         class="h-24 w-full object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity border-2 border-green-200"
                                         onclick="openImageModal('{{ Storage::url($attachment->file_path) }}')">
                                    <div class="absolute inset-0 rounded-lg ring-1 ring-inset ring-green-500/20"></div>
                                    <div class="absolute bottom-1 right-1 bg-green-500 text-white text-xs px-1 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Response Form -->
            <div id="responseSection" class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Respon Admin</h2>
                </div>
                <div class="px-6 py-4">
                    @if($complaint->admin_response)
                        <!-- Existing Response -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">Respon Admin:</span>
                                <span class="text-sm text-gray-500">
                                    {{ $complaint->updated_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="prose max-w-none text-gray-700">
                                {!! nl2br(e($complaint->admin_response)) !!}
                            </div>
                        </div>
                    @endif

                    <p class="text-gray-600">Gunakan button "Selesaikan" di atas untuk memberikan respon dan dokumentasi penyelesaian.</p>
                </div>
            </div>

            <!-- Comments/Updates -->
            @if($complaint->comments && $complaint->comments->count() > 0)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Riwayat Update</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            @foreach($complaint->comments as $comment)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full {{ $comment->user->hasRole('admin') ? 'bg-indigo-600' : 'bg-gray-600' }} flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">
                                                {{ substr($comment->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                            @if($comment->user->hasRole('admin'))
                                                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                                    Admin
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <!-- Status Summary -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Ringkasan Status</h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status Saat Ini:</span>
                        @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-50 text-yellow-800',
                            'in_progress' => 'bg-blue-50 text-blue-700',
                            'resolved' => 'bg-green-50 text-green-700',
                            'rejected' => 'bg-red-50 text-red-700',
                        ];
                        $statusLabels = ['pending' => 'Menunggu', 'in_progress' => 'Diproses', 'resolved' => 'Selesai', 'rejected' => 'Ditolak'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusClasses[$complaint->status] ?? 'bg-gray-50 text-gray-700' }}">
                            {{ $statusLabels[$complaint->status] ?? 'Unknown' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Prioritas:</span>
                        @php
                        $priorityClasses = [
                            'low' => 'bg-gray-50 text-gray-700',
                            'medium' => 'bg-yellow-50 text-yellow-700',
                            'high' => 'bg-orange-50 text-orange-700',
                            'urgent' => 'bg-red-50 text-red-700',
                        ];
                        $priorityLabels = ['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'urgent' => 'Mendesak'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $priorityClasses[$complaint->priority] ?? 'bg-gray-50 text-gray-700' }}">
                            {{ $priorityLabels[$complaint->priority] ?? 'Unknown' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Tanggal Dibuat:</span>
                        <span class="text-sm text-gray-900">{{ $complaint->created_at->format('d M Y') }}</span>
                    </div>
                    @if($complaint->responded_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Tanggal Respon:</span>
                            <span class="text-sm text-gray-900">{{ $complaint->responded_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Lama Penanganan:</span>
                        <span class="text-sm text-gray-900">
                            @php
                                $endDate = $complaint->status === 'resolved' ? $complaint->updated_at : now();
                                $diff = $complaint->created_at->diff($endDate);

                                if ($diff->days > 0) {
                                    echo $diff->days . ' hari';
                                    if ($diff->h > 0) echo ' ' . $diff->h . ' jam';
                                } elseif ($diff->h > 0) {
                                    echo $diff->h . ' jam';
                                    if ($diff->i > 0) echo ' ' . $diff->i . ' menit';
                                } elseif ($diff->i > 0) {
                                    echo $diff->i . ' menit';
                                } else {
                                    echo 'Baru saja';
                                }
                            @endphp
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Pelapor</h2>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center text-sm">
                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-gray-600">{{ $complaint->user->name }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-600">{{ $complaint->user->email }}</span>
                    </div>
                    @if($complaint->user->phone)
                        <div class="flex items-center text-sm">
                            <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-gray-600">{{ $complaint->user->phone }}</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            <strong>Total Keluhan:</strong> {{ $complaint->user->complaints()->count() }}
                        </div>
                        <div class="text-sm text-gray-600">
                            <strong>Keluhan Selesai:</strong> {{ $complaint->user->complaints()->where('status', 'resolved')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <p class="text-sm text-gray-600">Gunakan button status di bagian header untuk mengubah status keluhan.</p>

                    <a href="{{ route('admin.complaints.edit', $complaint) }}"
                       class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit Keluhan
                    </a>

                    <a href="{{ route('admin.complaints.print', $complaint) }}"
                       target="_blank"
                       class="w-full inline-flex justify-center items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-16.326 0C1.768 7.441 1 8.375 1 9.456v6.294A2.25 2.25 0 003.25 18h1.091m16.091 0V16.5l.5-3h-17l.5 3v1.5m15.591 0h-15.591" />
                        </svg>
                        Print Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Foto Keluhan</h3>
            <button onclick="closeImageModal()" class="text-black close-modal cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="text-center">
            <img id="modalImage" src="" alt="Foto keluhan" class="max-w-full h-auto rounded-lg">
        </div>
    </div>
</div>

<!-- Resolve Complaint Modal -->
<div id="resolveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Selesaikan Keluhan</h3>
            <button onclick="closeResolveModal()" class="text-black close-modal cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="resolveForm" method="POST" action="{{ route('admin.complaints.status', $complaint) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="resolved">

            <div class="space-y-4">
                <!-- Admin Response -->
                <div>
                    <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                        Respon Penyelesaian <span class="text-red-500">*</span>
                    </label>
                    <textarea id="admin_response"
                              name="admin_response"
                              rows="4"
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Jelaskan bagaimana keluhan ini diselesaikan...">{{ old('admin_response', $complaint->admin_response) }}</textarea>
                </div>

                <!-- Resolution Photos -->
                <div>
                    <label for="resolution_photos" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Dokumentasi Penyelesaian (Opsional)
                    </label>
                    <div id="photoDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <div class="space-y-2">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="text-sm text-gray-600">
                                <label for="resolution_photos" class="cursor-pointer text-green-600 hover:text-green-500">
                                    Upload foto dokumentasi
                                </label>
                                atau drag & drop di sini
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB (Maksimal 3 foto)</p>
                        </div>
                        <input type="file"
                               id="resolution_photos"
                               name="resolution_photos[]"
                               multiple
                               accept="image/*"
                               class="hidden"
                               onchange="handleResolutionPhotos(this)">
                    </div>

                    <!-- Preview Resolution Photos -->
                    <div id="resolutionPreview" class="mt-4 hidden"></div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeResolveModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Selesaikan Keluhan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function scrollToResponse() {
        document.getElementById('responseSection').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.getElementById('admin_response').focus();
    }

    // Status update functions
    function updateStatus(status) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.complaints.status", $complaint) }}';
        form.innerHTML = `
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);

        // Use beautiful modal
        showDeleteModal(`Apakah Anda yakin ingin mengubah status menjadi ${getStatusText(status)}?`, form);
    }

    function getStatusText(status) {
        switch(status) {
            case 'pending': return 'Menunggu';
            case 'in_progress': return 'Sedang Diproses';
            case 'resolved': return 'Selesai';
            case 'rejected': return 'Ditolak';
            default: return status;
        }
    }

    // Resolve modal functions
    function openResolveModal() {
        document.getElementById('resolveModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Clear form
        document.getElementById('admin_response').value = '';
        document.getElementById('resolution_photos').value = '';
        clearResolutionPreview();
    }

    // Resolution photos handling
    let resolutionFiles = [];

    function handleResolutionPhotos(input) {
        const files = Array.from(input.files);
        const maxFiles = 3;
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (files.length > maxFiles) {
            alert(`Maksimal ${maxFiles} foto dapat diupload`);
            input.value = '';
            return;
        }

        resolutionFiles = [];
        files.forEach(file => {
            if (file.size > maxSize) {
                alert(`File ${file.name} terlalu besar. Maksimal 2MB per file.`);
                return;
            }

            if (!file.type.match('image.*')) {
                alert(`File ${file.name} bukan file gambar.`);
                return;
            }

            resolutionFiles.push(file);
        });

        previewResolutionPhotos();
    }

    function previewResolutionPhotos() {
        const container = document.getElementById('resolutionPreview');
        container.innerHTML = '';

        if (resolutionFiles.length === 0) {
            container.classList.add('hidden');
            container.classList.remove('grid', 'grid-cols-3', 'gap-4');
            return;
        }

        container.classList.remove('hidden');
        container.classList.add('grid', 'grid-cols-3', 'gap-4');

        resolutionFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                        <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <button type="button"
                            onclick="removeResolutionPhoto(${index})"
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
        });
    }

    function removeResolutionPhoto(index) {
        resolutionFiles.splice(index, 1);
        updateResolutionFileInput();
        previewResolutionPhotos();
    }

    function updateResolutionFileInput() {
        const dt = new DataTransfer();
        resolutionFiles.forEach(file => dt.items.add(file));
        document.getElementById('resolution_photos').files = dt.files;
    }

    function clearResolutionPreview() {
        resolutionFiles = [];
        document.getElementById('resolutionPreview').innerHTML = '';
        document.getElementById('resolutionPreview').classList.add('hidden');
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Close modals when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.getElementById('resolveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeResolveModal();
            }
        });

        // Drag and drop for resolution photos
        const photoDropZone = document.getElementById('photoDropZone');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            photoDropZone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            photoDropZone.addEventListener(eventName, function() {
                photoDropZone.classList.add('border-green-400', 'bg-green-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            photoDropZone.addEventListener(eventName, function() {
                photoDropZone.classList.remove('border-green-400', 'bg-green-50');
            }, false);
        });

        photoDropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            // Update file input and handle files
            document.getElementById('resolution_photos').files = files;
            handleResolutionPhotos(document.getElementById('resolution_photos'));
        }, false);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            const resolveModal = document.getElementById('resolveModal');
            const imageModal = document.getElementById('imageModal');

            if (e.key === 'Escape') {
                if (!resolveModal.classList.contains('hidden')) {
                    closeResolveModal();
                } else if (!imageModal.classList.contains('hidden')) {
                    closeImageModal();
                }
            }
        });
    });
</script>
@endpush
@endsection
