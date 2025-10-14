@extends('layouts.admin')

@section('title', 'Laporan Keluhan')

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
                    <a href="{{ route('admin.reports.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Laporan</a>
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
                    Laporan Keluhan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Laporan detail dan analisis keluhan pengguna
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.reports.export', ['type' => 'complaints', 'format' => 'csv'] + request()->all()) }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'complaints', 'format' => 'json'] + request()->all()) }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export JSON
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <form method="GET" action="{{ route('admin.reports.complaints') }}" class="px-6 py-4">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Dalam Progress</option>
                        <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category_id" id="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <a href="{{ route('admin.reports.complaints') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Reset
                </a>
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7">
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Total</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Pending</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Progress</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['in_progress']) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Selesai</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['resolved']) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Ditolak</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['rejected']) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Tingkat Selesai</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['completion_rate'] }}%</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-3">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Rata-rata Respon</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['avg_response_time'] }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Daftar Keluhan
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Total {{ $complaints->total() }} keluhan ditemukan
            </p>
        </div>

        @if($complaints->count() > 0)
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($complaints as $complaint)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                <a href="{{ route('admin.complaints.show', $complaint) }}" class="hover:text-indigo-600">
                                                    {{ $complaint->title }}
                                                </a>
                                            </h4>
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                                @if($complaint->status === 'pending') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                                                @elseif($complaint->status === 'in_progress') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
                                                @elseif($complaint->status === 'resolved') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                                                @elseif($complaint->status === 'rejected') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10
                                                @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                                @if($complaint->status === 'pending') Pending
                                                @elseif($complaint->status === 'in_progress') Dalam Progress
                                                @elseif($complaint->status === 'resolved') Selesai
                                                @elseif($complaint->status === 'rejected') Ditolak
                                                @else {{ ucfirst($complaint->status) }} @endif
                                            </span>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 space-x-6">
                                            <div class="flex items-center">
                                                <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $complaint->user->name }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $complaint->category->name ?? 'Tidak ada kategori' }}
                                            </div>
                                            @if($complaint->location)
                                                <div class="flex items-center">
                                                    <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    {{ Str::limit($complaint->location, 30) }}
                                                </div>
                                            @endif
                                            <div class="flex items-center">
                                                <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $complaint->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($complaint->priority)
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                            @if($complaint->priority === 'high') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10
                                            @elseif($complaint->priority === 'medium') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                                            @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                            @if($complaint->priority === 'high') Tinggi
                                            @elseif($complaint->priority === 'medium') Sedang
                                            @else Rendah @endif
                                        </span>
                                    @endif
                                    <a href="{{ route('admin.complaints.show', $complaint) }}"
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $complaints->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada keluhan</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada keluhan yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>
@endsection
