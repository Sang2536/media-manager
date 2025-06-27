@extends('layouts.app')

@section('title', 'Tag Manager')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <x-head-content
                title-content="📁 Danh sách Tag"
                :route-action="[
                    'mode' => route('media-tags.index', ['view' => 'grid']),
                    'create' => route('media-tags.create'),
                    'destroy' => '#',
                ]"
                :button-dropdown="[
                    'title' => '⚙️ Tác vụ khác',
                    'items' => [
                        ['href' => '#', 'text' => '🔄 Làm mới'],
                        ['href' => '#', 'text' => '📂 Chuyển thư mục'],
                        ['href' => '#', 'text' => '📥 Tải xuống'],
                        ['href' => '#', 'text' => '✅ Chọn tất cả'],
                        ['href' => '#', 'text' => '🧾 Chi tiết'],
                        ['href' => '#', 'text' => '🖼 Xem nhanh'],
                        ['href' => '#', 'text' => '📌 Ghim'],
                        ['href' => '#', 'text' => '📤 Xuất ảnh'],
                        ['href' => '#', 'text' => '⚙️ Cài đặt hiển thị'],
                        ['href' => '#', 'text' => '♿ Chế độ truy cập'],
                    ],
                ]"
            />
        </div>

        <div class="flex flex-col">
            <div class="flex te flex-wrap gap-2">
                @forelse ($tags as $tag)
                    <x-tag :tag-name="$tag->name" :tag-id="$tag->id" :deletable="true" />
                @empty
                    <span class="text-gray-400 italic">Không có tag</span>
                @endforelse
            </div>
        </div>
    </div>
@endsection
