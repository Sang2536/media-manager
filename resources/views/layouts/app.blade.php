<!DOCTYPE html>

<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title', 'Media Manager')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- CDN - font Google --}}
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">

        {{-- CDN - Select --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        {{-- CDN - Tom Select --}}
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>


        {{-- Style Custom --}}
        <style>
            html, body {
                font-family: 'Noto Sans', sans-serif;
            }
        </style>

        {{-- Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-800">
        <div class="flex min-h-screen">
            {{-- Sidebar trái: 1/4 --}}
            @include('layouts.left-sidebar')

            {{-- Nội dung chính: 3/4 --}}
            <main class="w-3/4 p-6 overflow-auto">
                @yield('content')
            </main>
        </div>

        {{-- Custom JS --}}
        @stack('scripts')
    </body>
</html>

