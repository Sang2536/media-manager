@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-4">📊 Tổng quan</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500">📁 Thư mục</p>
            <h2 class="text-2xl font-semibold">{{ $totalFolders }}</h2>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500">🖼️ File Media</p>
            <h2 class="text-2xl font-semibold">{{ $totalFiles }}</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500 font-semibold mb-2">🏷️ Tags phổ biến</p>
            <ul class="list-disc ml-5">
                @foreach ($popularTags as $tag)
                    <li>{{ $tag->name }} ({{ $tag->files_count }})</li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500 font-semibold mb-2">🕒 Metadata gần đây</p>
            <ul class="text-sm">
                @foreach ($recentMetadata as $meta)
                    <li>{{ $meta->key }}: {{ Str::limit($meta->value, 30) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
