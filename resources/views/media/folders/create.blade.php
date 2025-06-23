@extends('layouts.app')

@section('title', 'Create Folder')

@section('content')
    <div class="max-w-xl mx-auto p-6">
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

            <div>
                <label for="name" class="block font-medium">Tên thư mục:</label>
                <input type="text" name="name" id="name"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                       value="{{ old('name') }}" required>
            </div>

            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Lưu thư mục
                </button>
                <a href="{{ route('media-folders.index') }}" class="ml-4 text-gray-600 hover:underline">⬅ Quay lại</a>
            </div>
        </form>
    </div>
@endsection
