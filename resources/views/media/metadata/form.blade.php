@extends('layouts.app')

@section('title', 'Create Metadata')

@section('content')
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">🆕 Thêm Metadata</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <strong>Đã xảy ra lỗi:</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $formAttr['route'] }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @php
                $method = $formAttr['action'] === 'create' ? 'POST' : 'PUT';
            @endphp
            @method($method)

            {{-- Select File --}}
            <div class="space-y-4">
                @php
                    $selectedId = ($formAttr['action'] === 'create' || isset($metadata->file)) ? $metadata->file->id : null;
                    $imgUrl = ($formAttr['action'] === 'create' || isset($metadata->file)) ? $metadata->file->image_url : '';
                @endphp

                <div>
                    <label for="file-select" class="block text-sm font-medium text-gray-700 mb-2">
                        Chọn file:
                    </label>
                    <select name="media_file_id" id="file-select" class="w-full border px-2 py-1 rounded">
                        <option value="" data-img="">-- Không có --</option>
                        @foreach ($files as $item)
                            <option value="{{ $item->id }}" data-img="{{ $item->image_url }}"
                                {{ $selectedId === $item->id ? 'selected' : '' }}>
                                {{ $item->user->name }} - {{ $item->id }} - {{ $item->path }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="image-select-container" class="mt-3">
                    <img id="select-image"
                         src="{{ $imgUrl }}"
                         alt="Image preview"
                         class="max-h-64 rounded border shadow {{ $imgUrl ? '' : 'hidden' }}">
                </div>
            </div>

            {{-- Metadata key-value động bằng JS --}}
            <x-metadata-field
                :metadata="(isset($metadata) && $metadata) ? [$metadata->toArray()] : []"
                :limit="1"
            />

            {{-- Actions--}}
            <div class="flex gap-4">
                <x-button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    name-btn="📤 Tải lên"
                />

                <x-button
                    type="reset"
                    class="inline-flex items-center gap-2 border border-b-blue-500 bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition shadow"
                    name-btn="🔄 Làm mới"
                />

                <x-button
                    :href="route('media-metadata.index')"
                    class="inline-flex items-center gap-2 border border-b-gray-600 bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition shadow"
                    name-btn="🔙 Quay lại"
                />
            </div>
        </form>
    </div>
@endsection
