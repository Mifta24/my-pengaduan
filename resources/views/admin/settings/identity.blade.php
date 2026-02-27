@extends('layouts.admin')

@section('title', 'Pengaturan Identitas Web')

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
                    <span class="ml-4 text-sm font-medium text-gray-500">Pengaturan Identitas</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Identitas Web</h1>
        <p class="mt-1 text-sm text-gray-600">Ubah nama sistem dan informasi identitas lingkungan.</p>
    </div>

    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <form action="{{ route('admin.settings.identity.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="site_name" class="block text-sm font-medium text-gray-900">Nama Lengkap Sistem</label>
                <input id="site_name" name="site_name" type="text" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
            </div>

            <div>
                <label for="short_name" class="block text-sm font-medium text-gray-900">Nama Singkat</label>
                <input id="short_name" name="short_name" type="text" value="{{ old('short_name', $settings['short_name'] ?? '') }}" required
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
            </div>

            <div>
                <label for="area_name" class="block text-sm font-medium text-gray-900">Nama Wilayah</label>
                <input id="area_name" name="area_name" type="text" value="{{ old('area_name', $settings['area_name'] ?? '') }}" required
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
            </div>

            <div>
                <label for="footer_text" class="block text-sm font-medium text-gray-900">Teks Footer</label>
                <input id="footer_text" name="footer_text" type="text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}"
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-900">Email Kontak</label>
                    <input id="contact_email" name="contact_email" type="text" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-900">Telepon Kontak</label>
                    <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Simpan Identitas Web
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
