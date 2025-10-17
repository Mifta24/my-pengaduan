@extends('layouts.admin')

@section('title', 'Pengguna - ' . $user->name)

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
                    <a href="{{ route('admin.users.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Pengguna</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $user->name }}</span>
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
                <div class="flex items-center space-x-4">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                            @if($user->avatar)
                                <img class="w-16 h-16 rounded-full object-cover" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <span class="text-xl font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                            {{ $user->name }}
                        </h1>

                        <div class="mt-1 flex items-center space-x-3">
                            <!-- Status badge -->
                            @if($user->is_active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <svg class="mr-1 h-3 w-3 fill-green-500" viewBox="0 0 6 6">
                                        <circle cx="3" cy="3" r="3" />
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                    <svg class="mr-1 h-3 w-3 fill-red-500" viewBox="0 0 6 6">
                                        <circle cx="3" cy="3" r="3" />
                                    </svg>
                                    Tidak Aktif
                                </span>
                            @endif

                            <!-- Role badge -->
                            @php
                                $userRole = $user->getRoleNames()->first();
                            @endphp
                            @if($userRole)
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                    @if($userRole === 'super_admin') bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20
                                    @elseif($userRole === 'admin') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
                                    @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                    @if($userRole === 'super_admin') Super Admin
                                    @elseif($userRole === 'admin') Admin
                                    @else User @endif
                                </span>
                            @endif

                            <!-- Email verification -->
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    ✓ Email Verified
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                    ! Email Belum Verified
                                </span>
                            @endif
                        </div>

                        <div class="mt-2 flex items-center text-sm text-gray-500 space-x-6">
                            <div class="flex items-center">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $user->email }}
                            </div>
                            @if($user->phone)
                                <div class="flex items-center">
                                    <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $user->phone }}
                                </div>
                            @endif
                            <div class="flex items-center">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Bergabung {{ $user->created_at->format('d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}"
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
                            <dd class="text-lg font-medium text-gray-900">{{ $user->complaints->count() }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Keluhan Selesai</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->complaints->where('status', 'resolved')->count() }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Keluhan Pending</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->complaints->where('status', 'pending')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Komentar</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $user->comments->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Complaints -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Keluhan Terbaru</h3>
                        <a href="{{ route('admin.complaints.index', ['user' => $user->id]) }}"
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($user->complaints->take(5) as $complaint)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        <a href="{{ route('admin.complaints.show', $complaint) }}" class="hover:text-indigo-600">
                                            {{ $complaint->title }}
                                        </a>
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $complaint->category->name }} • {{ $complaint->created_at->diffForHumans() }}
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
                            <p class="mt-1 text-sm text-gray-500">Pengguna ini belum pernah mengajukan keluhan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Komentar Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($user->comments->take(5) as $comment)
                        <div class="px-6 py-4">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm">
                                        <p class="text-gray-900">{{ Str::limit($comment->content, 100) }}</p>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <span>Pada keluhan:
                                            <a href="{{ route('admin.complaints.show', $comment->complaint) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ Str::limit($comment->complaint->title, 50) }}
                                            </a>
                                        </span>
                                        <span> • {{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada komentar</h3>
                            <p class="mt-1 text-sm text-gray-500">Pengguna ini belum pernah memberikan komentar.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Details -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detail Pengguna</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                    </div>

                    @if($user->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->phone }}</dd>
                        </div>
                    @endif

                    @if($user->address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->address }}</dd>
                        </div>
                    @endif

                    @if($user->rt_number || $user->rw_number)
                        <div class="grid grid-cols-2 gap-4">
                            @if($user->rt_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Lurah</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->rt_number }}</dd>
                                </div>
                            @endif
                            @if($user->rw_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">RW</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->rw_number }}</dd>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($user->nik)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIK</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $user->nik }}</dd>
                        </div>
                    @endif

                    @if($user->ktp_path)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Foto KTP</dt>
                            <dd class="mt-1">
                                <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($user->ktp_path) }}"
                                         alt="KTP {{ $user->name }}"
                                         class="w-full h-auto cursor-pointer hover:opacity-90 transition"
                                         onclick="openKTPModal()">
                                </div>
                                <button type="button"
                                        onclick="openKTPModal()"
                                        class="mt-2 text-sm text-indigo-600 hover:text-indigo-500 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                    Lihat ukuran penuh
                                </button>
                            </dd>
                        </div>

                        <!-- Verification Status -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Verifikasi KTP</dt>
                            <dd class="mt-1">
                                @if($user->is_verified)
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Terverifikasi
                                    </span>
                                    @if($user->verified_at)
                                        <p class="mt-1 text-xs text-gray-500">{{ $user->verified_at->format('d M Y, H:i') }}</p>
                                    @endif
                                @else
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Menunggu Verifikasi
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <!-- Verification Actions -->
                        @if(!$user->is_verified)
                            <div class="pt-2 border-t border-gray-200">
                                <dt class="text-sm font-medium text-gray-500 mb-3">Aksi Verifikasi</dt>
                                <dd class="space-y-2">
                                    <form action="{{ route('admin.users.verify-user', $user) }}" method="POST" class="inline-block w-full">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                                                onclick="return confirm('Apakah Anda yakin ingin memverifikasi pengguna ini?')">
                                            <svg class="mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Verifikasi Pengguna
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.reject-verification', $user) }}" method="POST" class="inline-block w-full">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                                                onclick="return confirm('Apakah Anda yakin ingin menolak verifikasi pengguna ini?')">
                                            <svg class="mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Tolak Verifikasi
                                        </button>
                                    </form>
                                </dd>
                            </div>
                        @else
                            <div class="pt-2 border-t border-gray-200">
                                <dt class="text-sm font-medium text-gray-500 mb-3">Aksi Verifikasi</dt>
                                <dd>
                                    <form action="{{ route('admin.users.reject-verification', $user) }}" method="POST" class="inline-block w-full">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full inline-flex justify-center items-center rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-600"
                                                onclick="return confirm('Apakah Anda yakin ingin membatalkan verifikasi pengguna ini?')">
                                            <svg class="mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Batalkan Verifikasi
                                        </button>
                                    </form>
                                </dd>
                            </div>
                        @endif
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Peran</dt>
                        <dd class="mt-1">
                            @if($userRole)
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                    @if($userRole === 'super_admin') bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20
                                    @elseif($userRole === 'admin') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20
                                    @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                    @if($userRole === 'super_admin') Super Admin
                                    @elseif($userRole === 'admin') Admin
                                    @else User @endif
                                </span>
                            @else
                                <span class="text-sm text-gray-500">Tidak ada peran</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($user->is_active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                    Tidak Aktif
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email Verification</dt>
                        <dd class="mt-1">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Verified {{ $user->email_verified_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                    Belum Verified
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bergabung</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d F Y, H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Update</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d F Y, H:i') }}</dd>
                    </div>

                    @if($user->last_login_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Login Terakhir</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->last_login_at->format('d F Y, H:i') }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit Pengguna
                    </a>

                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm
                                    @if($user->is_active) bg-yellow-600 text-white hover:bg-yellow-500 @else bg-green-600 text-white hover:bg-green-500 @endif">
                                @if($user->is_active)
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                    </svg>
                                    Nonaktifkan
                                @else
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aktifkan
                                @endif
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.complaints.index', ['user' => $user->id]) }}"
                       class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Semua Keluhan
                    </a>

                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua keluhan dan komentar yang terkait akan ikut terhapus.')" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                                    @if($user->complaints->count() > 0) onclick="return confirm('Pengguna ini memiliki {{ $user->complaints->count() }} keluhan. Yakin ingin menghapus?')" @endif>
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Hapus Pengguna
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KTP Preview Modal -->
@if($user->ktp_path)
<div id="ktpModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50" onclick="closeKTPModal()">
    <div class="relative top-10 mx-auto p-5 w-full max-w-4xl">
        <div class="relative bg-white rounded-lg shadow-xl">
            <!-- Close button -->
            <button type="button"
                    onclick="closeKTPModal()"
                    class="absolute top-4 right-4 text-gray-400 bg-white rounded-full p-2 hover:text-gray-600 hover:bg-gray-100 focus:outline-none z-10">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- KTP Image -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto KTP - {{ $user->name }}</h3>
                <img src="{{ Storage::url($user->ktp_path) }}"
                     alt="KTP {{ $user->name }}"
                     class="w-full h-auto rounded-lg"
                     onclick="event.stopPropagation()">
                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                    <p><strong>NIK:</strong> <span class="font-mono">{{ $user->nik }}</span></p>
                    @if($user->address)
                        <p><strong>Alamat:</strong> {{ $user->address }}</p>
                    @endif
                    @if($user->rt_number || $user->rw_number)
                        <p><strong>Lurah/RW:</strong> {{ $user->rt_number }}/{{ $user->rw_number }}</p>
                    @endif
                    @if($user->verified_at)
                        <p class="text-green-600 mt-2 flex items-center">
                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Diverifikasi pada: {{ $user->verified_at->format('d M Y, H:i') }}
                        </p>
                    @else
                        <p class="text-yellow-600 mt-2 flex items-center">
                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Menunggu verifikasi admin
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function openKTPModal() {
        document.getElementById('ktpModal').classList.remove('hidden');
    }

    function closeKTPModal() {
        document.getElementById('ktpModal').classList.add('hidden');
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeKTPModal();
        }
    });
</script>
@endpush
