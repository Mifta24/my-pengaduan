@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Kelola Pengumuman
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola pengumuman untuk warga Lurah/RW
            </p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('admin.announcements.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Pengumuman
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pengumuman</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Aktif</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['published'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Draft</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['draft'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tidak Aktif</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['unpublished'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
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
                    <select name="status"
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Semua Status</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="unpublished" {{ request('status') === 'unpublished' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="sm:w-40">
                    <select name="priority"
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Semua Prioritas</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Mendesak</option>
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
                    @if(request()->hasAny(['search', 'status', 'priority']))
                        <a href="{{ route('admin.announcements.index') }}"
                           class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        @if($announcements->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $announcements->total() }} Pengumuman
                    @if(request()->hasAny(['search', 'status', 'priority']))
                        (filtered)
                    @endif
                </h2>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($announcements as $announcement)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        <a href="{{ route('admin.announcements.show', $announcement->id) }}"
                                           class="hover:text-indigo-600 transition-colors duration-150">
                                            {{ $announcement->title }}
                                        </a>
                                    </h3>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $announcement->is_active
                                        ? 'bg-green-50 text-green-700 ring-green-600/20'
                                        : 'bg-red-50 text-red-700 ring-red-600/10' }}">
                                        {{ $announcement->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                    @php
                                    $priorityClasses = [
                                        'low' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                        'medium' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'high' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                                        'urgent' => 'bg-red-50 text-red-700 ring-red-600/10',
                                    ];
                                    $priorityLabels = ['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'urgent' => 'Mendesak'];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $priorityClasses[$announcement->priority] ?? $priorityClasses['low'] }}">
                                        {{ $priorityLabels[$announcement->priority] ?? 'Rendah' }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit(strip_tags($announcement->content), 150) }}
                                </p>

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
                                        {{ $announcement->created_at->format('d M Y, H:i') }}
                                    </span>
                                    @if($announcement->published_at)
                                        <span class="flex items-center">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Dipublikasi: {{ $announcement->published_at->format('d M Y') }}
                                        </span>
                                    @endif
                                    @if($announcement->scheduled_at && $announcement->scheduled_at->isFuture())
                                        <span class="flex items-center text-orange-600">
                                            <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Terjadwal: {{ $announcement->scheduled_at->format('d M Y, H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-6 flex items-center space-x-2">
                                <!-- Quick Actions -->
                                @if(!$announcement->is_active)
                                    <form method="POST" action="{{ route('admin.announcements.publish', $announcement->slug) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center rounded-md bg-green-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-green-500"
                                                title="Publikasikan">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.announcements.unpublish', $announcement->slug) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center rounded-md bg-gray-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-gray-500"
                                                title="Sembunyikan">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.announcements.edit', $announcement->slug) }}"
                                   class="inline-flex items-center rounded-md bg-indigo-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500"
                                   title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="inline-flex items-center rounded-md bg-gray-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-gray-500"
                                            title="More options">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                         class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                         style="display: none;">
                                        <a href="{{ route('admin.announcements.show', $announcement->slug) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Lihat Detail
                                        </a>
                                        <form method="POST" action="{{ route('admin.announcements.duplicate', $announcement->slug) }}" class="block">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Duplikasi
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement->slug) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $announcements->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada pengumuman</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'status', 'priority']))
                        Tidak ada pengumuman yang sesuai dengan filter yang dipilih.
                    @else
                        Mulai dengan membuat pengumuman pertama Anda.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'status', 'priority']))
                        <a href="{{ route('admin.announcements.index') }}"
                           class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Reset Filter
                        </a>
                    @else
                        <a href="{{ route('admin.announcements.create') }}"
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Buat Pengumuman
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection
