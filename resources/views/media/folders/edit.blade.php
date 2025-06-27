@extends('layouts.app')

@section('title', 'Update Folder')

@section('content-header', 'Update Folder')

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">✏️ Sửa thư mục</h1>

        {{-- Breadcrumb --}}
        @if ($breadcrumbs)
            <x-breadcrumb
                :breadcrumbs="$breadcrumbs"
                view-mode="grid"
                :route-action="[
                    'index' => route('media-folders.index')
                ]"
                :current="$folder->name"
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

        <form action="{{ route('media-folders.update', $folder->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <x-tabbed-folder-editor
                :breadcrumb-path="$breadcrumbPath"
                :render-folder-options="$renderFolderOptions"
                :folder-name="$folder->name"
                mode="edit"
            />

            <div class="flex items-center gap-3">
                <x-button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition" name-btn="💾 Cập nhật" />

                <x-button :href="route('media-folders.index')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition" name-btn="🔙 Quay lại" />
            </div>
        </form>
    </div>
@endsection
