@extends('layouts.app')

@section('title', 'Detail Keluhan - ' . $complaint->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div class="flex">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Beranda</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <a href="{{ route('complaints.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Keluhan Saya</a>
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
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                        @if($complaint->status === 'pending') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                        @elseif($complaint->status === 'in_progress') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                        @elseif($complaint->status === 'completed') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                        @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
                        @if($complaint->status === 'pending') Menunggu
                        @elseif($complaint->status === 'in_progress') Diproses
                        @elseif($complaint->status === 'completed') Selesai
                        @else Ditolak @endif
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                        @if($complaint->priority === 'low') bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20
                        @elseif($complaint->priority === 'medium') bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20
                        @elseif($complaint->priority === 'high') bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20
                        @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
                        @if($complaint->priority === 'low') Rendah
                        @elseif($complaint->priority === 'medium') Sedang
                        @elseif($complaint->priority === 'high') Tinggi
                        @else Mendesak @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    <a href="{{ route('complaints.index') }}"
                       class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Daftar
                    </a>
                    @if($complaint->user_id === Auth::id() && $complaint->status === 'pending')
                        <a href="{{ route('complaints.edit', $complaint->id) }}"
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            Edit Keluhan
                        </a>
                    @endif
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
            @if($complaint->attachments && $complaint->attachments->count() > 0)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Foto Pendukung</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $complaint->attachments->count() }} foto</p>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach($complaint->attachments as $attachment)
                                <div class="relative group">
                                    <div class="aspect-square overflow-hidden rounded-lg bg-gray-100">
                                        <img src="{{ Storage::url($attachment->file_path) }}"
                                             alt="Foto keluhan"
                                             class="h-full w-full object-cover cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openImageModal('{{ Storage::url($attachment->file_path) }}', '{{ $attachment->file_name }}')">
                                    </div>
                                    <div class="absolute inset-0 rounded-lg ring-1 ring-inset ring-gray-900/10"></div>

                                    <!-- File info overlay -->
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent rounded-b-lg p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <p class="text-xs text-white truncate">{{ $attachment->file_name }}</p>
                                        <p class="text-xs text-gray-300">{{ number_format($attachment->file_size / 1024, 1) }} KB</p>
                                    </div>

                                    <!-- View fullscreen icon -->
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="bg-black/50 rounded-full p-1">
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Response -->
            @if($complaint->admin_response)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Respon Admin</h2>
                            @if($complaint->responded_at)
                                <span class="text-sm text-gray-500">
                                    {{ $complaint->responded_at->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($complaint->admin_response)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Comments -->
            @if($complaint->comments && $complaint->comments->count() > 0)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Komentar & Update</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            @foreach($complaint->comments as $comment)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">
                                                {{ substr($comment->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
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
            <!-- Status Timeline -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Timeline Status</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Keluhan Diterima</p>
                                                <p class="text-sm text-gray-500">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if($complaint->status !== 'pending')
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Keluhan Diproses</p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $complaint->updated_at->format('d M Y, H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if(in_array($complaint->status, ['completed', 'rejected']))
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full {{ $complaint->status === 'completed' ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white">
                                                    @if($complaint->status === 'completed')
                                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        Keluhan {{ $complaint->status === 'completed' ? 'Selesai' : 'Ditolak' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $complaint->responded_at ? $complaint->responded_at->format('d M Y, H:i') : $complaint->updated_at->format('d M Y, H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Kontak</h2>
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3">
        <div class="bg-white rounded-lg shadow-2xl">
            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Foto Keluhan</h3>
                    <p id="modalImageInfo" class="text-sm text-gray-600"></p>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="downloadBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                    </button>
                    <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Image Content -->
            <div class="p-4 bg-gray-50">
                <div class="text-center">
                    <img id="modalImage" src="" alt="Foto keluhan" class="max-w-full max-h-[70vh] h-auto rounded-lg shadow-md mx-auto">
                </div>

                <!-- Image Navigation (if multiple images) -->
                <div id="imageNavigation" class="flex justify-between items-center mt-4 hidden">
                    <button id="prevImage" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <span id="imageCounter" class="text-gray-600 font-medium"></span>
                    <button id="nextImage" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentImages = [];
    let currentImageIndex = 0;

    function openImageModal(imageSrc, imageIndex = 0) {
        // Get all images from the complaint
        const imageElements = document.querySelectorAll('[onclick*="openImageModal"]');
        currentImages = [];

        imageElements.forEach((element, index) => {
            const onclickValue = element.getAttribute('onclick');
            const srcMatch = onclickValue.match(/openImageModal\('([^']+)'/);
            if (srcMatch) {
                currentImages.push({
                    src: srcMatch[1],
                    name: `Foto ${index + 1}`,
                    element: element
                });
            }
        });

        currentImageIndex = imageIndex;
        showCurrentImage();

        // Show navigation if multiple images
        const navigation = document.getElementById('imageNavigation');
        if (currentImages.length > 1) {
            navigation.classList.remove('hidden');
            navigation.classList.add('flex');
            updateImageCounter();
        } else {
            navigation.classList.add('hidden');
            navigation.classList.remove('flex');
        }

        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function showCurrentImage() {
        if (currentImages.length > 0 && currentImageIndex >= 0 && currentImageIndex < currentImages.length) {
            const currentImage = currentImages[currentImageIndex];
            document.getElementById('modalImage').src = currentImage.src;
            document.getElementById('modalImageInfo').textContent = currentImage.name;

            // Update download button
            const downloadBtn = document.getElementById('downloadBtn');
            downloadBtn.onclick = () => downloadImage(currentImage.src, currentImage.name);
        }
    }

    function updateImageCounter() {
        const counter = document.getElementById('imageCounter');
        counter.textContent = `${currentImageIndex + 1} dari ${currentImages.length}`;
    }

    function previousImage() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            showCurrentImage();
            updateImageCounter();
        }
    }

    function nextImage() {
        if (currentImageIndex < currentImages.length - 1) {
            currentImageIndex++;
            showCurrentImage();
            updateImageCounter();
        }
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
        currentImages = [];
        currentImageIndex = 0;
    }

    function downloadImage(imageSrc, imageName) {
        const link = document.createElement('a');
        link.href = imageSrc;
        link.download = imageName || 'foto-keluhan.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageModal');
            if (!modal.classList.contains('hidden')) {
                switch(e.key) {
                    case 'Escape':
                        closeImageModal();
                        break;
                    case 'ArrowLeft':
                        previousImage();
                        break;
                    case 'ArrowRight':
                        nextImage();
                        break;
                }
            }
        });

        // Navigation button events
        document.getElementById('prevImage').addEventListener('click', previousImage);
        document.getElementById('nextImage').addEventListener('click', nextImage);
    });
</script>
@endpush
@endsection
