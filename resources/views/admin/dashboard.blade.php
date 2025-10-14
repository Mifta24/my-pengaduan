@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div class="flex">
                    <span class="text-sm font-medium text-gray-500">Dashboard</span>
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
                    Dashboard Admin
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Selamat datang kembali, {{ Auth::user()->name }}! Berikut adalah ringkasan aktivitas hari ini.
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    Laporan
                </a>
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

    <!-- Statistics cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Complaints -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-indigo-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Keluhan</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ $totalComplaints ?? 0 }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                    <svg class="h-4 w-4 flex-shrink-0 self-center text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Increased by</span>
                    12%
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.complaints.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Lihat semua<span class="sr-only"> Total Keluhan stats</span>
                        </a>
                    </div>
                </div>
            </dd>
        </div>

        <!-- Total Users -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-green-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Pengguna</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                    <svg class="h-4 w-4 flex-shrink-0 self-center text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Increased by</span>
                    8%
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.users.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Lihat semua<span class="sr-only"> Total Pengguna stats</span>
                        </a>
                    </div>
                </div>
            </dd>
        </div>

        <!-- Completion Rate -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-yellow-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Tingkat Penyelesaian</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ $completionRate ?? 0 }}%</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                    <svg class="h-4 w-4 flex-shrink-0 self-center text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Increased by</span>
                    5%
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.reports.complaints', ['status' => 'resolved']) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Lihat keluhan selesai<span class="sr-only"> Tingkat Penyelesaian stats</span>
                        </a>
                    </div>
                </div>
            </dd>
        </div>

        <!-- Average Response Time -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-purple-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Rata-rata Respon</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ $avgResponseTime }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-red-600">
                    <svg class="h-4 w-4 flex-shrink-0 self-center text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l4.96-4.96a.75.75 0 111.06 1.06l-6.25 6.25a.75.75 0 01-1.06 0l-6.25-6.25a.75.75 0 111.06-1.06L9.25 14.388V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Decreased by</span>
                    2%
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.reports.complaints') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Analisis respon<span class="sr-only"> Rata-rata Respon stats</span>
                        </a>
                    </div>
                </div>
            </dd>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Status Distribution Chart -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Distribusi Status Keluhan</h3>
                <div class="space-y-3">
                    @if(isset($complaintsByStatus))
                        @foreach(['pending' => ['label' => 'Menunggu', 'color' => 'bg-yellow-500'], 'in_progress' => ['label' => 'Diproses', 'color' => 'bg-blue-500'], 'resolved' => ['label' => 'Selesai', 'color' => 'bg-green-500'], 'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-500']] as $status => $config)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 {{ $config['color'] }} rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-600">{{ $config['label'] }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $complaintsByStatus[$status] ?? 0 }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Complaints -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Keluhan Terbaru</h3>
                    <a href="{{ route('admin.complaints.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Lihat semua
                    </a>
                </div>
                <div class="space-y-3">
                    @if(isset($recentComplaints) && $recentComplaints->count() > 0)
                        @foreach($recentComplaints as $complaint)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $complaint->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $complaint->user->name }}</p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($complaint->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($complaint->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada keluhan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    @if(isset($complaintsByCategory) && $complaintsByCategory->count() > 0)
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Keluhan per Kategori</h3>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($complaintsByCategory as $categoryData)
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center"
                             style="background-color: {{ $categoryData['color'] }}20; color: {{ $categoryData['color'] }}">
                            <span class="text-2xl font-bold">{{ $categoryData['total'] }}</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-600">{{ $categoryData['category'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // You can add Chart.js or other chart libraries here for more advanced visualizations
    console.log('Dashboard loaded successfully');
</script>
@endpush
