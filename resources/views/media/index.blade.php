@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-6">📂 Danh sách Media</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($mediaFiles as $file)
                <div class="bg-white shadow-lg rounded-2xl overflow-hidden transition hover:scale-[1.02] duration-300">
{{--                <img src="{{ asset('storage/' . $file->thumbnail_path ?? $file->path) }}"--}}
                    <img src="{{ $file->path }}"
                         alt="{{ $file->original_name }}"
                         class="w-full h-48 object-cover" />

                    <div class="p-4">
                        <h2 class="text-lg font-semibold truncate">{{ $file->original_name }}</h2>

                        <p class="text-sm text-gray-500 mt-1">
                            📂 {{ $file->folder->name ?? 'Không có' }}<br>
                            👤 {{ $file->user->name }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach ($file->tags as $tag)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $tag->name }}</span>
                            @endforeach
                        </div>

                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            @foreach ($file->metadata as $meta)
                                <div>🔹 <strong>{{ $meta->key }}:</strong> {{ $meta->value }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $mediaFiles->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
