@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            📊 Tổng quan hệ thống
        </h1>
        <p class="text-gray-500">Thông tin nhanh về thư mục, media, thẻ và metadata</p>
    </div>

    {{-- Tổng số --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-100 to-blue-50 p-4 rounded-2xl shadow flex items-center gap-4">
            <div class="text-blue-600 text-3xl">📁</div>
            <div>
                <p class="text-sm text-gray-500">Thư mục</p>
                <h2 class="text-xl font-bold text-gray-800">{{ $totalFolders }}</h2>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-100 to-green-50 p-4 rounded-2xl shadow flex items-center gap-4">
            <div class="text-green-600 text-3xl">🖼️</div>
            <div>
                <p class="text-sm text-gray-500">File Media</p>
                <h2 class="text-xl font-bold text-gray-800">{{ $totalFiles }}</h2>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-100 to-purple-50 p-4 rounded-2xl shadow flex items-center gap-4">
            <div class="text-purple-600 text-3xl">🏷️</div>
            <div>
                <p class="text-sm text-gray-500">Tags</p>
                <h2 class="text-xl font-bold text-gray-800">{{ $totalTags ?? '--' }}</h2>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-100 to-yellow-50 p-4 rounded-2xl shadow flex items-center gap-4">
            <div class="text-yellow-600 text-3xl">🕒</div>
            <div>
                <p class="text-sm text-gray-500">Metadata gần đây</p>
                <h2 class="text-xl font-bold text-gray-800">{{ count($recentMetadata) }}</h2>
            </div>
        </div>
    </div>

    {{-- Danh sách chi tiết --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                🖼️ Chi tiết File Media
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                @if ($mediaFileDetails)
                <div class="p-4 rounded-xl bg-blue-50">
                    <p class="text-sm text-gray-500">Tổng dung lượng</p>
                    <h4 class="text-lg font-bold text-blue-600">{{ format_bytes($mediaFileDetails['totalSize']) ?? 0 }}</h4>
                </div>
                <div class="p-4 rounded-xl bg-green-50">
                    <p class="text-sm text-gray-500">Số ảnh</p>
                    <h4 class="text-lg font-bold text-green-600">{{ $mediaFileDetails['imageCount'] }}</h4>
                </div>
                <div class="p-4 rounded-xl bg-yellow-50">
                    <p class="text-sm text-gray-500">Số video</p>
                    <h4 class="text-lg font-bold text-yellow-600">{{ $mediaFileDetails['videoCount'] }}</h4>
                </div>
                <div class="p-4 rounded-xl bg-purple-50">
                    <p class="text-sm text-gray-500">Loại phổ biến</p>
                    <h4 class="text-lg font-bold text-purple-600">{{ $mediaFileDetails['mostCommonType'] ?? 'N/A' }}</h4>
                </div>
                @else
                    <p class="text-sm text-gray-400 italic">Không có file nào gần đây.</p>
                @endif
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-2">🆕 File tải lên gần đây</h4>
                @if (!empty($recentFiles) && count($recentFiles))
                    <ul class="text-sm space-y-1">
                        @foreach ($recentFiles as $file)
                            <li class="flex justify-between text-gray-700 border-b py-1 last:border-b-0">
                                <span class="truncate w-2/3" title="{{ $file->name }}">{{ Str::limit($file->name, 30) }}</span>
                                <span class="text-gray-500 text-xs">{{ $file->created_at->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400 italic">Không có file nào gần đây.</p>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                🏷️ Tags phổ biến
            </h3>
            @if ($popularTags->isEmpty())
                <p class="text-sm text-gray-400 italic">Chưa có tag nào.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($popularTags as $tag)
                        <li class="flex justify-between text-sm">
                            <span>{{ $tag->name }}</span>
                            <span class="text-gray-500">({{ $tag->files_count }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                🕒 Metadata gần đây
            </h3>
            @if ($recentMetadata->isEmpty())
                <p class="text-sm text-gray-400 italic">Không có metadata nào gần đây.</p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach ($recentMetadata as $meta)
                        <li class="border-b last:border-b-0 pb-2">
                            <strong>{{ $meta->key }}:</strong> {{ Str::limit($meta->value, 40) }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
