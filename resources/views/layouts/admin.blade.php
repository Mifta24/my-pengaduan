<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ $appIdentity['site_name'] ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full" x-data="{ sidebarOpen: false }" x-cloak>
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="relative z-50 lg:hidden">
            <div class="fixed inset-0 bg-gray-900/80" x-on:click="sidebarOpen = false"></div>

            <!-- Mobile sidebar -->
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1">
                    <!-- Close button -->
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" class="-m-2.5 p-2.5" x-on:click="sidebarOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile sidebar component -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                        <!-- Logo -->
                        <div class="flex h-16 shrink-0 items-center">
                            <h2 class="text-xl font-bold text-indigo-600">{{ $appIdentity['short_name'] ?? ($appIdentity['site_name'] ?? config('app.name')) }}</h2>
                            <span class="ml-2 rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700">Admin</span>
                        </div>

                        <!-- Mobile Navigation -->
                        <nav class="flex flex-1 flex-col">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <li>
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <!-- Dashboard -->
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                                      {{ request()->routeIs('admin.dashboard')
                                                         ? 'bg-indigo-50 text-indigo-600'
                                                         : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                                </svg>
                                                Dashboard
                                            </a>
                                        </li>

                                        <!-- Complaints -->
                                        <li>
                                            <a href="{{ route('admin.complaints.index') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                                      {{ request()->routeIs('admin.complaints.*')
                                                         ? 'bg-indigo-50 text-indigo-600'
                                                         : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                </svg>
                                                Keluhan
                                                @if($pendingCount = \App\Models\Complaint::where('status', 'pending')->count())
                                                    <span class="ml-auto w-9 min-w-max whitespace-nowrap rounded-full bg-red-50 px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-red-600 ring-1 ring-inset ring-red-200">
                                                        {{ $pendingCount }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>

                                        <!-- Categories -->
                                        <li>
                                            <a href="{{ route('admin.categories.index') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                                      {{ request()->routeIs('admin.categories.*')
                                                         ? 'bg-indigo-50 text-indigo-600'
                                                         : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                                </svg>
                                                Kategori
                                            </a>
                                        </li>

                                        <!-- Users -->
                                        <li>
                                            <a href="{{ route('admin.users.index') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                                      {{ request()->routeIs('admin.users.*')
                                                         ? 'bg-indigo-50 text-indigo-600'
                                                         : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                                </svg>
                                                Pengguna
                                            </a>
                                        </li>

                                        <!-- Announcements -->
                                        <li>
                                            <a href="{{ route('admin.announcements.index') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                                      {{ request()->routeIs('admin.announcements.*')
                                                         ? 'bg-indigo-50 text-indigo-600'
                                                         : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                                                </svg>
                                                Pengumuman
                                            </a>
                                        </li>

                                        <!-- Reports -->
                                        <li>
                                            <a href="{{ route('admin.reports.index') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                                      {{ request()->routeIs('admin.reports.*')
                                                         ? 'bg-indigo-50 text-indigo-600'
                                                         : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                                </svg>
                                                Laporan
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- User section -->
                                <li class="mt-auto">
                                    <div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900">
                                        <img class="h-8 w-8 rounded-full bg-gray-50"
                                             src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7C3AED&background=EDE9FE"
                                             alt="">
                                        <span class="sr-only">{{ Auth::user()->name }}</span>
                                        <span aria-hidden="true">{{ Auth::user()->name }}</span>
                                    </div>
                                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                                        <li>
                                            <a href="{{ route('profile.edit') }}"
                                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50"
                                               x-on:click="sidebarOpen = false">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Profil
                                            </a>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50 w-full text-left">
                                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                                    </svg>
                                                    Keluar
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-xl ring-1 ring-gray-900/10">
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center">
                    <h2 class="text-2xl font-bold text-indigo-600">{{ $appIdentity['short_name'] ?? ($appIdentity['site_name'] ?? config('app.name')) }}</h2>
                    <span class="ml-2 rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700">Admin</span>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <!-- Dashboard -->
                                <li>
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.dashboard')
                                                 ? 'bg-indigo-50 text-indigo-600'
                                                 : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        Dashboard
                                    </a>
                                </li>

                                <!-- Complaints -->
                                <li>
                                    <a href="{{ route('admin.complaints.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.complaints.*')
                                                 ? 'bg-indigo-50 text-indigo-600'
                                                 : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        Keluhan
                                        @if($pendingCount = \App\Models\Complaint::where('status', 'pending')->count())
                                            <span class="ml-auto w-9 min-w-max whitespace-nowrap rounded-full bg-red-50 px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-red-600 ring-1 ring-inset ring-red-200">
                                                {{ $pendingCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>

                                <!-- Categories -->
                                <li>
                                    <a href="{{ route('admin.categories.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.categories.*')
                                                 ? 'bg-indigo-50 text-indigo-600'
                                                 : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                        </svg>
                                        Kategori
                                    </a>
                                </li>

                                <!-- Users -->
                                <li>
                                    <a href="{{ route('admin.users.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.users.*')
                                                 ? 'bg-indigo-50 text-indigo-600'
                                                 : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        Pengguna
                                    </a>
                                </li>

                                <!-- Announcements -->
                                <li>
                                    <a href="{{ route('admin.announcements.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.announcements.*')
                                                 ? 'bg-indigo-50 text-indigo-600'
                                                 : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                                        </svg>
                                        Pengumuman
                                    </a>
                                </li>

                                <!-- Reports -->
                                <li>
                                    <a href="{{ route('admin.reports.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.reports.*')
                                                 ? 'bg-indigo-50 text-indigo-600'
                                                 : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                        </svg>
                                        Laporan
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Settings Section -->
                        <li class="mt-auto">
                            <div class="text-xs font-semibold leading-6 text-gray-400">Pengaturan</div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Profil
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.settings.identity.edit') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.identity.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0m-15 0H3m16.5 0H21m-9-7.5v15" />
                                        </svg>
                                        Identitas Web
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-red-600 hover:bg-gray-50">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                            </svg>
                                            Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main content area -->
        <div class="lg:pl-72">
            <!-- Top navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden cursor-pointer" x-on:click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>

                <!-- Breadcrumb -->
                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex items-center gap-x-2">
                        @yield('breadcrumb')
                    </div>
                </div>

                <!-- User menu -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button type="button"
                            class="flex items-center gap-x-4 px-4 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50 cursor-pointer"
                            x-on:click="userMenuOpen = !userMenuOpen">
                        <img class="h-8 w-8 rounded-full bg-gray-50"
                             src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7C3AED&background=EDE9FE"
                             alt="">
                        <span class="hidden lg:flex lg:items-center">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="userMenuOpen"
                         x-transition
                         x-on:click.away="userMenuOpen = false"
                         class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-3 py-1 text-left text-sm leading-6 text-gray-900 hover:bg-gray-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-6 lg:py-10">
                <div class="px-4 sm:px-6 lg:px-8 max-w-full">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="mb-6 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.73 10.42a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="mb-6 rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Page content with responsive wrapper -->
                    <div class="w-full overflow-x-auto">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDeleteModal()"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <!-- Icon -->
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <!-- Content -->
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Konfirmasi Penghapusan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="deleteMessage">
                                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteBtn" onclick="confirmDelete()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Ya, Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        let deleteForm = null;
        let deleteCallback = null;

        /**
         * Show delete confirmation modal
         * @param {string} message - Custom message to display
         * @param {HTMLFormElement|Function} formOrCallback - Form to submit or callback function
         */
        function showDeleteModal(message, formOrCallback) {
            const modal = document.getElementById('deleteModal');
            const messageEl = document.getElementById('deleteMessage');

            if (message) {
                messageEl.textContent = message;
            } else {
                messageEl.textContent = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.';
            }

            if (typeof formOrCallback === 'function') {
                deleteCallback = formOrCallback;
                deleteForm = null;
            } else {
                deleteForm = formOrCallback;
                deleteCallback = null;
            }

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            // Focus on cancel button for accessibility
            setTimeout(() => {
                modal.querySelector('button[onclick="closeDeleteModal()"]')?.focus();
            }, 100);
        }

        /**
         * Close delete confirmation modal
         */
        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            deleteForm = null;
            deleteCallback = null;
        }

        /**
         * Confirm and execute delete action
         */
        function confirmDelete() {
            if (deleteCallback && typeof deleteCallback === 'function') {
                deleteCallback();
            } else if (deleteForm) {
                deleteForm.submit();
            }
            closeDeleteModal();
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Auto-attach to delete buttons with data-confirm attribute
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete buttons with data-confirm
            document.querySelectorAll('[data-confirm-delete]').forEach(element => {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const message = this.getAttribute('data-confirm-delete') || 'Apakah Anda yakin ingin menghapus data ini?';

                    // Find the form to submit
                    let form = this.closest('form');
                    if (!form && this.hasAttribute('data-form')) {
                        form = document.getElementById(this.getAttribute('data-form'));
                    }

                    if (form) {
                        showDeleteModal(message, form);
                    }
                });
            });

            // Handle forms with data-confirm-delete
            document.querySelectorAll('form[data-confirm-delete]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!form.hasAttribute('data-confirmed')) {
                        e.preventDefault();
                        const message = form.getAttribute('data-confirm-delete') || 'Apakah Anda yakin ingin menghapus data ini?';

                        showDeleteModal(message, function() {
                            form.setAttribute('data-confirmed', 'true');
                            form.submit();
                        });
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
