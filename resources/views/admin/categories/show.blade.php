@extends('layouts.admin')

@section('title', 'Kategori - ' . $category->name)

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
                    <a href="{{ route('admin.categories.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Kategori</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $category->name }}</span>
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
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-3">
                    <!-- Category Icon -->
                    @if($category->icon)
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-2xl
                                @if($category->color === 'blue') bg-blue-100
                                @elseif($category->color === 'green') bg-green-100
                                @elseif($category->color === 'yellow') bg-yellow-100
                                @elseif($category->color === 'red') bg-red-100
                                @elseif($category->color === 'purple') bg-purple-100
                                @elseif($category->color === 'indigo') bg-indigo-100
                                @elseif($category->color === 'pink') bg-pink-100
                                @else bg-gray-100 @endif">
                                @if($category->icon === 'building') 🏢
                                @elseif($category->icon === 'road') 🛣️
                                @elseif($category->icon === 'trash') 🗑️
                                @elseif($category->icon === 'security') 🛡️
                                @elseif($category->icon === 'water') 💧
                                @elseif($category->icon === 'electric') ⚡
                                @elseif($category->icon === 'health') 🏥
                                @elseif($category->icon === 'education') 🎓
                                @elseif($category->icon === 'transport') 🚌
                                @elseif($category->icon === 'park') 🌳
                                @elseif($category->icon === 'sport') ⚽
                                @else 📝 @endif
                            </div>
                        </div>
                    @endif

                    <div>
                        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                            {{ $category->name }}
                        </h1>

                        <!-- Status badge -->
                        <div class="mt-1 flex items-center space-x-2">
                            @if($category->is_active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <svg class="mr-1 h-3 w-3 fill-green-500" viewBox="0 0 6 6">
                                        <circle cx="3" cy="3" r="3" />
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                    <svg class="mr-1 h-3 w-3 fill-gray-400" viewBox="0 0 6 6">
                                        <circle cx="3" cy="3" r="3" />
                                    </svg>
                                    Tidak Aktif
                                </span>
                            @endif

                            @if($category->color)
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                    @if($category->color === 'blue') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
                                    @elseif($category->color === 'green') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                                    @elseif($category->color === 'yellow') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                                    @elseif($category->color === 'red') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10
                                    @elseif($category->color === 'purple') bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20
                                    @elseif($category->color === 'indigo') bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20
                                    @elseif($category->color === 'pink') bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20
                                    @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                    {{ ucfirst($category->color) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($category->description)
                    <p class="mt-2 text-sm text-gray-500">{{ $category->description }}</p>
                @endif
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Keluhan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->where('status', 'pending')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Dalam Progress</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->where('status', 'in_progress')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $category->complaints->where('status', 'resolved')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Recent Complaints -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Keluhan Terbaru</h3>
                        <a href="{{ route('admin.complaints.index', ['category' => $category->id]) }}"
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($category->complaints->take(5) as $complaint)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        <a href="{{ route('admin.complaints.show', $complaint) }}" class="hover:text-indigo-600">
                                            {{ $complaint->title }}
                                        </a>
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Oleh {{ $complaint->user->name }} • {{ $complaint->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
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
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada keluhan</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada keluhan yang masuk untuk kategori ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Category Details -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detail Kategori</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">{{ $category->slug }}</dd>
                    </div>

                    @if($category->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->description }}</dd>
                        </div>
                    @endif

                    @if($category->icon)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Icon</dt>
                            <dd class="mt-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg">
                                        @if($category->icon === 'building') 🏢
                                        @elseif($category->icon === 'road') 🛣️
                                        @elseif($category->icon === 'trash') 🗑️
                                        @elseif($category->icon === 'security') 🛡️
                                        @elseif($category->icon === 'water') 💧
                                        @elseif($category->icon === 'electric') ⚡
                                        @elseif($category->icon === 'health') 🏥
                                        @elseif($category->icon === 'education') 🎓
                                        @elseif($category->icon === 'transport') 🚌
                                        @elseif($category->icon === 'park') 🌳
                                        @elseif($category->icon === 'sport') ⚽
                                        @else 📝 @endif
                                    </span>
                                    <span class="text-sm text-gray-900">{{ ucfirst($category->icon) }}</span>
                                </div>
                            </dd>
                        </div>
                    @endif

                    @if($category->color)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Warna Theme</dt>
                            <dd class="mt-1">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 rounded
                                        @if($category->color === 'blue') bg-blue-500
                                        @elseif($category->color === 'green') bg-green-500
                                        @elseif($category->color === 'yellow') bg-yellow-500
                                        @elseif($category->color === 'red') bg-red-500
                                        @elseif($category->color === 'purple') bg-purple-500
                                        @elseif($category->color === 'indigo') bg-indigo-500
                                        @elseif($category->color === 'pink') bg-pink-500
                                        @else bg-gray-500 @endif"></div>
                                    <span class="text-sm text-gray-900">{{ ucfirst($category->color) }}</span>
                                </div>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($category->is_active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                    Tidak Aktif
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('d F Y, H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Update</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('d F Y, H:i') }}</dd>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <a href="{{ route('admin.categories.edit', $category) }}"
                       class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit Kategori
                    </a>

                    <a href="{{ route('admin.complaints.index', ['category' => $category->id]) }}"
                       class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Semua Keluhan
                    </a>

                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                data-confirm-delete="Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'?@if($category->complaints->count() > 0) Kategori ini memiliki {{ $category->complaints->count() }} keluhan yang akan kehilangan kategori.@else Tindakan ini tidak dapat dibatalkan.@endif"
                                class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            Hapus Kategori
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
