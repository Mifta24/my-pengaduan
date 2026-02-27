@extends('layouts.app')

@section('title', 'Beranda - ' . ($appIdentity['site_name'] ?? config('app.name')))

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                MyPengaduan
            </h1>
            <p class="text-xl md:text-2xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                Sampaikan keluhan dan aspirasi Anda dengan mudah dan cepat.
                Kami berkomitmen untuk meningkatkan kualitas pelayanan di lingkungan Gang Annur 2 RT 05.
            </p>
            @auth
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('complaints.create') }}"
                       class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajukan Keluhan
                    </a>
                    <a href="{{ route('complaints.index') }}"
                       class="inline-flex items-center px-8 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-indigo-600 md:py-4 md:text-lg md:px-10">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Lihat Keluhan Saya
                    </a>
                </div>
            @else
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-8 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-indigo-600 md:py-4 md:text-lg md:px-10">
                        Masuk
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Fitur Unggulan
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Sistem yang dirancang untuk memudahkan komunikasi antara warga dan admin RT
            </p>
        </div>

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="relative group">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Pengajuan Mudah</h3>
                        <p class="text-gray-600">Ajukan keluhan dengan mudah melalui formulir online yang user-friendly.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="relative group">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tracking Real-time</h3>
                        <p class="text-gray-600">Pantau status keluhan Anda secara real-time dan terima notifikasi update.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="relative group">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Respon Cepat</h3>
                        <p class="text-gray-600">Tim pengurus siap merespon keluhan Anda dengan cepat dan professional.</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="relative group">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Komunikasi Transparan</h3>
                        <p class="text-gray-600">Komunikasi dua arah yang transparan antara warga dan admin RT.</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="relative group">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan Berkala</h3>
                        <p class="text-gray-600">Dapatkan laporan berkala tentang progress dan penyelesaian berbagai keluhan.</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="relative group">
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Mobile Friendly</h3>
                        <p class="text-gray-600">Akses sistem dari smartphone Anda kapan saja dan dimana saja.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Statistik Sistem
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Data real-time aktivitas penggunaan sistem pengaduan warga
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $stats['total_complaints'] ?? 0 }}</div>
                <div class="text-gray-600">Total Keluhan</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['resolved_complaints'] ?? 0 }}</div>
                <div class="text-gray-600">Keluhan Selesai</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $stats['pending_complaints'] ?? 0 }}</div>
                <div class="text-gray-600">Keluhan Pending</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total_users'] ?? 0 }}</div>
                <div class="text-gray-600">Pengguna Aktif</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Announcements -->
@if(isset($announcements) && $announcements->count() > 0)
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Pengumuman Terbaru
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Informasi terkini dari admin RT
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
            @foreach($announcements as $announcement)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            Pengumuman
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ $announcement->created_at->format('d M Y') }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        <a href="{{ route('announcements.show', $announcement->id) }}" class="hover:text-indigo-600">
                            {{ $announcement->title }}
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ Str::limit($announcement->content, 150) }}
                    </p>
                    <a href="{{ route('announcements.show', $announcement->id) }}"
                       class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                        Baca selengkapnya →
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('announcements.index') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                Lihat Semua Pengumuman
            </a>
        </div>
    </div>
</div>
@endif

<!-- CTA Section -->
<div class="bg-indigo-600">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
            <span class="block">Punya keluhan atau saran?</span>
            <span class="block text-indigo-200">Sampaikan sekarang juga!</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                @auth
                    <a href="{{ route('complaints.create') }}"
                       class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Ajukan Keluhan
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Daftar Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
