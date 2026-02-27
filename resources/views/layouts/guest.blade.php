<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}{{ $appIdentity['site_name'] ?? config('app.name') }}</title>
        <meta name="description" content="{{ $appIdentity['site_name'] ?? config('app.name') }} - Platform digital untuk warga melaporkan keluhan ke admin RT.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex">
            <!-- Left Side - Branding -->
            <div class="hidden lg:flex lg:flex-1 lg:flex-col lg:justify-center lg:px-8 xl:px-12 bg-gradient-to-br from-indigo-600 to-purple-700">
                <div class="max-w-md">
                    <div class="flex items-center space-x-3 mb-8">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-white rounded-lg flex items-center justify-center">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M9 7h6m-6 4h6m-6 4h6" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ $appIdentity['short_name'] ?? ($appIdentity['site_name'] ?? config('app.name')) }}</h1>
                            <p class="text-indigo-200 text-sm">{{ $appIdentity['area_name'] ?? 'Gang Annur 2 RT 05' }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h2 class="text-3xl font-bold text-white leading-tight">
                            Platform Digital untuk Pengaduan Warga
                        </h2>
                        <p class="text-lg text-indigo-100">
                            Laporkan keluhan dengan mudah dan dapatkan respon cepat dari admin RT.
                        </p>

                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-indigo-100">Respon cepat dalam 24 jam</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-indigo-100">Tracking status real-time</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-indigo-100">Data aman & terprivasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="flex-1 flex flex-col justify-center px-4 py-12 sm:px-6 lg:px-8 xl:px-12">
                <div class="mx-auto w-full max-w-md">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden text-center mb-8">
                        <div class="inline-flex items-center space-x-2">
                            <div class="h-10 w-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M9 7h6m-6 4h6m-6 4h6" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">{{ $appIdentity['short_name'] ?? ($appIdentity['site_name'] ?? config('app.name')) }}</h1>
                                <p class="text-sm text-gray-500">{{ $appIdentity['area_name'] ?? 'Gang Annur 2 RT 05' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white py-8 px-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-500">
                            © {{ date('Y') }} {{ $appIdentity['footer_text'] ?? ($appIdentity['site_name'] ?? config('app.name')) }}.
                        </p>
                        <div class="mt-2 flex justify-center space-x-4 text-sm text-gray-400">
                            <a href="#" class="hover:text-gray-600">Bantuan</a>
                            <span>•</span>
                            <a href="#" class="hover:text-gray-600">Privasi</a>
                            <span>•</span>
                            <a href="#" class="hover:text-gray-600">Syarat & Ketentuan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts Stack -->
        @stack('scripts')
    </body>
</html>
