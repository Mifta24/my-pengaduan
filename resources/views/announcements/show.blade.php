@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="sr-only">Dashboard</span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <a href="{{ route('announcements.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Pengumuman</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500 truncate">{{ $announcement->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Content -->
    <article class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
        <!-- Sticky Banner for Important Announcements -->
        @if($announcement->is_sticky)
            <div class="bg-gradient-to-r from-amber-400 to-orange-500">
                <div class="px-6 py-4">
                    <div class="flex items-center space-x-3 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold">Pengumuman Penting</h3>
                            <p class="text-sm text-white/90">Harap dibaca dengan seksama</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="px-6 py-8">
            <!-- Header -->
            <header class="mb-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-3xl font-bold text-gray-900 leading-tight mb-4">
                            {{ $announcement->title }}
                        </h1>

                        @if($announcement->summary)
                            <p class="text-lg text-gray-600 leading-relaxed mb-6">
                                {{ $announcement->summary }}
                            </p>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-8 w-8 rounded-full"
                                         src="{{ $announcement->author->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($announcement->author->name) . '&color=7c3aed&background=ede9fe' }}"
                                         alt="{{ $announcement->author->name }}">
                                </div>
                                <div class="ml-3">
                                    <span class="font-medium text-gray-900">{{ $announcement->author->name }}</span>
                                    <span class="text-gray-500"> • {{ $announcement->author->getRoleNames()->first() ?? 'Pengurus' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Dipublikasikan {{ $announcement->published_at->format('d M Y, H:i') }}
                            </div>

                            @if($announcement->updated_at > $announcement->published_at)
                                <div class="flex items-center">
                                    <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Diperbarui {{ $announcement->updated_at->format('d M Y, H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Priority Badge -->
                    <div class="flex-shrink-0 ml-6">
                        <span class="inline-flex items-center rounded-full px-3 py-2 text-sm font-medium
                            @if($announcement->priority === 'urgent') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10
                            @elseif($announcement->priority === 'high') bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20
                            @elseif($announcement->priority === 'medium') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
                            @else bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20 @endif">
                            @if($announcement->priority === 'urgent') 🚨 Mendesak
                            @elseif($announcement->priority === 'high') ⚠️ Tinggi
                            @elseif($announcement->priority === 'medium') ℹ️ Sedang
                            @else 📝 Rendah @endif
                        </span>
                    </div>
                </div>

                <!-- Target Audience -->
                @if($announcement->target_audience && count($announcement->target_audience) > 0)
                    <div class="flex items-center space-x-3 mb-6 p-4 bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <div>
                            <span class="text-sm font-medium text-blue-900">Ditujukan untuk:</span>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($announcement->target_audience as $target)
                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                                        @if($target === 'all') 👥 Semua Warga
                                        @elseif($target === 'rt') 🏛️ Pengurus RT
                                        @else {{ ucfirst($target) }} @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="shareAnnouncement('{{ $announcement->title }}', '{{ route('announcements.show', $announcement->slug) }}')"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                            </svg>
                            Bagikan
                        </button>

                        <button type="button"
                                class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            Simpan
                        </button>
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $announcement->views_count ?? 0 }} kali dilihat
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="prose max-w-none prose-lg prose-indigo">
                {!! nl2br(e($announcement->content)) !!}
            </div>

            <!-- Attachments & Photos -->
            @if($announcement->attachments && count($announcement->attachments) > 0)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">📎 Lampiran & Foto</h3>

                    @php
                        $images = [];
                        $files = [];
                        foreach($announcement->attachments as $attachment) {
                            $extension = pathinfo($attachment['original_name'] ?? $attachment['name'] ?? '', PATHINFO_EXTENSION);
                            if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $images[] = $attachment;
                            } else {
                                $files[] = $attachment;
                            }
                        }
                    @endphp

                    <!-- Photo Gallery -->
                    @if(count($images) > 0)
                        <div class="mb-8">
                            <h4 class="text-base font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Foto ({{ count($images) }})
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($images as $index => $image)
                                    <div class="relative group cursor-pointer overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300"
                                         onclick="openImageModal('{{ asset('storage/' . $image['path']) }}', '{{ $image['original_name'] ?? $image['name'] ?? 'Foto ' . ($index + 1) }}')">
                                        <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                                            <img src="{{ asset('storage/' . $image['path']) }}"
                                                 alt="{{ $image['original_name'] ?? $image['name'] ?? 'Foto' }}"
                                                 class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300"
                                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect width=\'200\' height=\'200\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'14\' fill=\'%23999\'%3EGambar tidak tersedia%3C/text%3E%3C/svg%3E';">
                                        </div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                            <p class="text-sm font-medium truncate">{{ $image['original_name'] ?? $image['name'] ?? 'Foto ' . ($index + 1) }}</p>
                                            <p class="text-xs text-gray-200 mt-1">Klik untuk memperbesar</p>
                                        </div>
                                        <div class="absolute top-2 right-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            🔍 Lihat
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Other Files/Documents -->
                    @if(count($files) > 0)
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Dokumen ({{ count($files) }})
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($files as $file)
                                    @php
                                        $extension = pathinfo($file['original_name'] ?? $file['name'] ?? '', PATHINFO_EXTENSION);
                                    @endphp
                                    <a href="{{ asset('storage/' . $file['path']) }}"
                                       target="_blank"
                                       download
                                       class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 hover:shadow-md transition-all duration-200 group">
                                        <div class="flex-shrink-0">
                                            @if(strtolower($extension) === 'pdf')
                                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                                    <svg class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                                    <svg class="h-7 w-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600">
                                                {{ $file['original_name'] ?? $file['name'] ?? 'File' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ strtoupper($extension) }} •
                                                {{ isset($file['size']) ? number_format($file['size'] / 1024, 1) . ' KB' : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="ml-3">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Comments Section -->
            @if($announcement->allow_comments)
                <div id="comments" class="mt-12 pt-8 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Komentar ({{ $announcement->comments_count ?? 0 }})
                        </h3>
                    </div>

                    <!-- Comment Form -->
                    @auth
                        <form action="{{ route('announcements.comments.store', $announcement) }}" method="POST" class="mb-8">
                            @csrf
                            <div class="flex space-x-4">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full"
                                         src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=7c3aed&background=ede9fe' }}"
                                         alt="{{ auth()->user()->name }}">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div>
                                        <label for="content" class="sr-only">Tulis komentar</label>
                                        <textarea id="content" name="content" rows="3"
                                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                  placeholder="Tulis komentar Anda..." required></textarea>
                                    </div>
                                    <div class="mt-3 flex justify-end">
                                        <button type="submit"
                                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                            Kirim Komentar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center mb-8">
                            <p class="text-gray-600 mb-4">Silakan login untuk memberikan komentar</p>
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Login
                            </a>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    @if($announcement->comments && $announcement->comments->count() > 0)
                        <div class="space-y-6">
                            @foreach($announcement->comments as $comment)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex space-x-4">
                                        <div class="flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full"
                                                 src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&color=7c3aed&background=ede9fe' }}"
                                                 alt="{{ $comment->user->name }}">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</h4>
                                                @if($comment->user->hasRole(['admin', 'Lurah', 'rw']))
                                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                        {{ $comment->user->getRoleNames()->first() }}
                                                    </span>
                                                @endif
                                                <span class="text-sm text-gray-500">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada komentar</h3>
                            <p class="mt-1 text-sm text-gray-500">Jadilah yang pertama memberikan komentar pada pengumuman ini.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </article>

    <!-- Related or Previous/Next Announcements -->
    <div class="mt-12">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Pengumuman Lainnya</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($relatedAnnouncements ?? collect() as $related)
                <article class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start space-x-4">
                        @if($related->is_sticky)
                            <div class="flex-shrink-0">
                                <div class="h-2 w-2 bg-amber-400 rounded-full"></div>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-semibold text-gray-900 leading-tight mb-2">
                                <a href="{{ route('announcements.show', $related->slug) }}"
                                   class="hover:text-indigo-600 transition-colors duration-150">
                                    {{ Str::limit($related->title, 60) }}
                                </a>
                            </h4>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                {{ Str::limit(strip_tags($related->content), 100) }}
                            </p>
                            <div class="flex items-center text-xs text-gray-500">
                                <span>{{ $related->published_at->format('d M Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $related->author->name }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-7xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-4xl font-bold z-10">
            &times;
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[90vh] object-contain">
        <div class="text-center mt-4">
            <p id="modalImageName" class="text-white text-lg"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function shareAnnouncement(title, url) {
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(console.error);
        } else {
            // Fallback to clipboard
            navigator.clipboard.writeText(url).then(() => {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg z-50';
                toast.textContent = 'Link disalin ke clipboard!';
                document.body.appendChild(toast);

                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            }).catch(() => {
                // Fallback for older browsers
                window.open('https://wa.me/?text=' + encodeURIComponent(title + ' - ' + url));
            });
        }
    }

    function openImageModal(src, name) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalImageName = document.getElementById('modalImageName');

        modalImage.src = src;
        modalImageName.textContent = name;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside the image
    document.getElementById('imageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
@endpush
@endsection
