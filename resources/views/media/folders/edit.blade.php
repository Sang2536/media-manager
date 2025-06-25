@extends('layouts.app')

@section('title', 'Update Folder')

@section('content')
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">✏️ Sửa thư mục</h1>

        {{-- Breadcrumb --}}
        @if ($breadcrumbs)
            <x-breadcrumb
                :breadcrumbs="$breadcrumbs"
                view-mode="grid"
                :route-action="[
                    'index' => route('media-folders.index')
                ]"
                :current-folder="str()->slug($folder->name)"
            />
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('media-folders.update', $folder) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Chọn thư mục cha --}}
            <div class="py-2 space-y-2">
                {!! $renderFolderOptions !!}
            </div>

            {{-- Tên thư mục --}}
            <div class="py-2 space-y-2">
                <label class="block font-semibold mb-1">📝 Tên Thư mục</label>
                <input type="text" name="name"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                       placeholder="Tên folder"
                       value="{{ old('name', $folder->name) }}" required>
            </div>

            {{-- Button --}}
            <div>
                <x-button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                    name-btn="💾 Lưu thư mục"
                />

                <x-button
                    class="inline-flex items-center gap-2 border border-b-blue-500 bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition shadow"
                    name-btn="🔄 Làm mới"
                />

                <x-button
                    :href="route('media-folders.index')"
                    class="inline-flex items-center gap-2 border border-b-gray-600 bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition shadow"
                    name-btn="🔙 Quay lại"
                />
            </div>
        </form>
    </div>
@endsection
