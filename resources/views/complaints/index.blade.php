@extends('layouts.app')

@section('title', 'Keluhan Saya - ' . ($appIdentity['site_name'] ?? config('app.name')))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="border-b border-gray-200 pb-5 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Keluhan Saya
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola dan pantau status keluhan yang telah Anda ajukan.
                </p>
            </div>
            <div>
                <a href="{{ route('complaints.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Ajukan Keluhan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 px-4 py-3 border border-gray-200 rounded-lg mb-6">
        <form method="GET" action="{{ route('complaints.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari keluhan..."
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            <div>
                <select name="status"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('complaints.index') }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Complaints List -->
    @if($complaints->count() > 0)
        <div class="space-y-6">
            @foreach($complaints as $complaint)
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('complaints.show', $complaint->id) }}"
                                       class="hover:text-indigo-600">
                                        {{ $complaint->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-3">
                                    {{ Str::limit($complaint->description, 200) }}
                                </p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    @if($complaint->category)
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                            {{ $complaint->category->name }}
                                        </span>
                                    @endif
                                    <span>{{ $complaint->created_at->format('d M Y, H:i') }}</span>
                                    @if($complaint->location)
                                        <span>📍 {{ $complaint->location }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-6 flex flex-col items-end space-y-2">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                                    @if($complaint->status === 'pending') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                                    @elseif($complaint->status === 'in_progress') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                                    @elseif($complaint->status === 'resolved') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                                    @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
                                    @if($complaint->status === 'pending') Menunggu
                                    @elseif($complaint->status === 'in_progress') Diproses
                                    @elseif($complaint->status === 'resolved') Selesai
                                    @else Ditolak @endif
                                </span>
                                <div class="flex space-x-2">
                                    <a href="{{ route('complaints.show', $complaint->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                    @if($complaint->status === 'pending')
                                        <a href="{{ route('complaints.edit', $complaint->id) }}"
                                           class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($complaint->admin_response)
                        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-sm font-medium text-gray-900">Respon Admin</span>
                                        @if($complaint->responded_at)
                                            <span class="text-xs text-gray-500">
                                                {{ $complaint->responded_at->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $complaint->admin_response }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $complaints->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada keluhan</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'status']))
                    Tidak ada keluhan yang sesuai dengan filter Anda.
                @else
                    Anda belum pernah mengajukan keluhan.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('complaints.index') }}"
                       class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Hapus Filter
                    </a>
                @else
                    <a href="{{ route('complaints.create') }}"
                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajukan Keluhan Pertama
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
