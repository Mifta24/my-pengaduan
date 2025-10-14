@extends('layouts.admin')

@section('title', 'Manajemen Keluhan')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
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
                    <span class="ml-4 text-sm font-medium text-gray-500">Keluhan</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Manajemen Keluhan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola semua keluhan yang diterima dari warga RT/RW.
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.complaints.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Keluhan
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 px-4 py-3 border border-gray-200 rounded-lg">
        <form method="GET" action="{{ route('admin.complaints.index') }}" class="flex flex-wrap items-center gap-4">
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
            <div>
                <select name="category_id"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'category_id']))
                <a href="{{ route('admin.complaints.index') }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($complaints->count() > 0)
                <!-- Mobile Card View (hidden on lg+) -->
                <div class="lg:hidden space-y-4">
                    @foreach($complaints as $complaint)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                        <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="hover:text-indigo-600">
                                            {{ $complaint->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $complaint->description }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $complaint->user->name }}
                                        </span>
                                        @if($complaint->category)
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                                {{ $complaint->category->name }}
                                            </span>
                                        @endif
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $complaint->report_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-col items-end gap-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                        @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($complaint->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $complaint->status === 'pending' ? 'Menunggu' :
                                           ($complaint->status === 'in_progress' ? 'Diproses' :
                                           ($complaint->status === 'resolved' ? 'Selesai' : 'Ditolak')) }}
                                    </span>
                                    <div class="flex gap-1">
                                        <a href="{{ route('admin.complaints.show', $complaint->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900 text-xs">Lihat</a>
                                        <a href="{{ route('admin.complaints.edit', $complaint->id) }}"
                                           class="text-gray-600 hover:text-gray-900 text-xs">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Keluhan
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Pelapor
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Kategori
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Tanggal
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($complaints as $complaint)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900">
                                                    <a href="{{ route('admin.complaints.show', $complaint->id) }}"
                                                       class="hover:text-indigo-600">
                                                        {{ Str::limit($complaint->title, 50) }}
                                                    </a>
                                                </div>
                                                <div class="text-gray-500 text-sm">
                                                    {{ Str::limit($complaint->description, 80) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div class="text-gray-900">{{ $complaint->user->name }}</div>
                                        <div class="text-gray-500">{{ $complaint->user->phone ?? 'Tidak ada telepon' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($complaint->category)
                                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                {{ $complaint->category->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                            @if($complaint->status === 'pending') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                                            @elseif($complaint->status === 'in_progress') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                                            @elseif($complaint->status === 'resolved') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                                            @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
                                            @if($complaint->status === 'pending') Menunggu
                                            @elseif($complaint->status === 'in_progress') Diproses
                                            @elseif($complaint->status === 'resolved') Selesai
                                            @else Ditolak @endif
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div>{{ $complaint->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $complaint->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.complaints.show', $complaint->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                            <a href="{{ route('admin.complaints.edit', $complaint->id) }}"
                                               class="text-gray-600 hover:text-gray-900">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $complaints->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada keluhan</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada keluhan yang terdaftar dalam sistem.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.complaints.create') }}"
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Keluhan Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
