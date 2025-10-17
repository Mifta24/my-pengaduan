@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

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
                    <span class="ml-4 text-sm font-medium text-gray-500">Pengguna</span>
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
                    Manajemen Pengguna
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola pengguna sistem dan atur peran mereka.
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pengguna
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 px-4 py-3 border border-gray-200 rounded-lg">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari pengguna..."
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            <div>
                <select name="role"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Semua Peran</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div>
                <select name="status"
                        class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($users->count() > 0)
                <!-- Mobile Card View (hidden on lg+) -->
                <div class="lg:hidden space-y-4">
                    @foreach($users as $user)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</h3>
                                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                            @if($user->phone)
                                                <p class="text-xs text-gray-500 mt-1">{{ $user->phone }}</p>
                                            @endif
                                        </div>
                                        <div class="ml-2 flex flex-col items-end gap-1">
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                                        @if($role->name === 'super_admin') bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-700/10
                                                        @elseif($role->name === 'admin') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                                                        @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                                        @if($role->name === 'super_admin') Super Admin
                                                        @elseif($role->name === 'admin') Admin
                                                        @else User @endif
                                                    </span>
                                                @endforeach
                                            @endif
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                                @if($user->email_verified_at) bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                                                @else bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20 @endif">
                                                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span>Bergabung {{ $user->created_at->format('d M Y') }}</span>
                                        @if($user->complaints_count > 0)
                                            <span>• {{ $user->complaints_count }} keluhan</span>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900 text-xs">Lihat</a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                           class="text-gray-600 hover:text-gray-900 text-xs">Edit</a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 text-xs"
                                                        onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
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
                                    Pengguna
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Kontak
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Peran
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Verifikasi KTP
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Bergabung
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div>{{ $user->phone ?? 'Tidak ada' }}</div>
                                        <div class="text-gray-400">{{ $user->address ?? 'Alamat tidak diisi' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium mr-1
                                                    @if($role->name === 'super_admin') bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-700/10
                                                    @elseif($role->name === 'admin') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                                                    @else bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10 @endif">
                                                    @if($role->name === 'super_admin') Super Admin
                                                    @elseif($role->name === 'admin') Admin
                                                    @else User @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                            @if($user->is_active) bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                                            @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 @endif">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($user->ktp_path)
                                            @if($user->is_verified)
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                                                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Menunggu
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                Belum Upload
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                               class="text-gray-600 hover:text-gray-900">Edit</a>
                                            @if($user->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="text-{{ $user->is_active ? 'red' : 'green' }}-600 hover:text-{{ $user->is_active ? 'red' : 'green' }}-900"
                                                            onclick="return confirm('Yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} pengguna ini?')">
                                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada pengguna</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'role', 'status']))
                            Tidak ada pengguna yang sesuai dengan filter Anda.
                        @else
                            Belum ada pengguna yang terdaftar.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Reset Filter
                            </a>
                        @else
                            <a href="{{ route('admin.users.create') }}"
                               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah Pengguna Pertama
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
