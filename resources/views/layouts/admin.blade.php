<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'MyPengaduan') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 z-50 flex w-72 flex-col" x-data="{ sidebarOpen: false }">
            <!-- Sidebar backdrop for mobile -->
            <div x-show="sidebarOpen" class="fixed inset-0 bg-gray-900/80 lg:hidden" x-on:click="sidebarOpen = false"></div>

            <!-- Sidebar component -->
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-xl ring-1 ring-gray-900/10">
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center">
                    <h2 class="text-2xl font-bold text-indigo-600">MyPengaduan</h2>
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
                                    <a href="{{ route('admin.reports') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                              {{ request()->routeIs('admin.reports')
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
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" x-on:click="sidebarOpen = true">
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
                            class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50"
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
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
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

                    <!-- Page content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>
