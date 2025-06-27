@extends('layouts.app')

@section('title', 'Create Folder')

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">➕ Tạo thư mục mới</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('media-folders.store') }}" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <x-tabbed-folder-editor
                :render-folder-options="$renderFolderOptions"
                mode="edit"
            />

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
