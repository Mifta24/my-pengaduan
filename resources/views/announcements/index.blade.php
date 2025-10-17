@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Pengumuman
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Pengumuman terbaru dari pengurus Lurah/RW
        </p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('announcements.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                <div class="flex-1">
                    <label for="search" class="sr-only">Cari pengumuman</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input id="search" name="search" type="text"
                               placeholder="Cari pengumuman..."
                               value="{{ request('search') }}"
                               class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="sm:w-40">
                    <select name="priority"
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Semua Prioritas</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Mendesak</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'priority']))
                        <a href="{{ route('announcements.index') }}"
                           class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Announcements List -->
    @if($announcements->count() > 0)
        <div class="space-y-6">
            @foreach($announcements as $announcement)
                <article class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Sticky Header for Important Announcements -->
                    @if($announcement->is_sticky)
                        <div class="bg-gradient-to-r from-amber-400 to-orange-500 px-6 py-2">
                            <div class="flex items-center space-x-2 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                <span class="text-sm font-semibold">Pengumuman Penting</span>
                            </div>
                        </div>
                    @endif

                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h2 class="text-xl font-bold text-gray-900 leading-tight">
                                        <a href="{{ route('announcements.show', $announcement->slug) }}"
                                           class="hover:text-indigo-600 transition-colors duration-150">
                                            {{ $announcement->title }}
                                        </a>
                                    </h2>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                        @if($announcement->priority === 'urgent') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10
                                        @elseif($announcement->priority === 'high') bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20
                                        @elseif($announcement->priority === 'medium') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
                                        @else bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20 @endif">
                                        @if($announcement->priority === 'urgent') Mendesak
                                        @elseif($announcement->priority === 'high') Tinggi
                                        @elseif($announcement->priority === 'medium') Sedang
                                        @else Rendah @endif
                                    </span>
                                </div>

                                @if($announcement->summary)
                                    <p class="text-gray-600 text-sm mb-3 leading-relaxed">
                                        {{ $announcement->summary }}
                                    </p>
                                @endif

                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $announcement->author->name }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $announcement->published_at->format('d M Y, H:i') }}
                                    </span>
                                    @if($announcement->comments_count > 0)
                                        <span class="flex items-center">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            {{ $announcement->comments_count }} komentar
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Content Preview -->
                        <div class="prose max-w-none text-gray-700 mb-4">
                            <p class="line-clamp-3">
                                {{ Str::limit(strip_tags($announcement->content), 200) }}
                            </p>
                        </div>

                        <!-- Tags/Target Audience -->
                        @if($announcement->target_audience && count($announcement->target_audience) > 0)
                            <div class="flex items-center space-x-2 mb-4">
                                <span class="text-sm text-gray-500">Target:</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($announcement->target_audience as $target)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            @if($target === 'all') Semua Warga
                                            @elseif($target === 'Lurah') Pengurus Lurah
                                            @elseif($target === 'rw') Pengurus RW
                                            @else {{ ucfirst($target) }} @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('announcements.show', $announcement->slug) }}"
                                   class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    Baca Selengkapnya
                                    <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>

                                @if($announcement->allow_comments)
                                    <a href="{{ route('announcements.show', $announcement->slug) }}#comments"
                                       class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Komentar
                                    </a>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Share Button -->
                                <button type="button" onclick="shareAnnouncement('{{ $announcement->title }}', '{{ route('announcements.show', $announcement->slug) }}')"
                                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                    </svg>
                                </button>

                                <!-- Bookmark -->
                                <button type="button"
                                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $announcements->withQueryString()->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada pengumuman</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'priority']))
                    Tidak ada pengumuman yang sesuai dengan filter yang dipilih.
                @else
                    Belum ada pengumuman yang dipublikasikan.
                @endif
            </p>
            @if(request()->hasAny(['search', 'priority']))
                <div class="mt-6">
                    <a href="{{ route('announcements.index') }}"
                       class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>
    @endif
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
                // You could show a toast notification here
                alert('Link disalin ke clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                window.open('https://wa.me/?text=' + encodeURIComponent(title + ' - ' + url));
            });
        }
    }
</script>
@endpush
@endsection
