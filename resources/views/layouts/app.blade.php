<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Media Manager')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">
    {{-- Sidebar trái: 1/4 --}}
    @include('layouts.sidebar-left')

    {{-- Nội dung chính: 3/4 --}}
    <main class="w-3/4 p-6">
        @yield('content')
    </main>

    @stack('scripts')
</div>
</body>
</html>

