@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">✏️ Sửa thư mục</h1>

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

            <div>
                <label for="name" class="block font-medium">Tên thư mục:</label>
                <input type="text" name="name" id="name"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                       value="{{ old('name', $folder->name) }}" required>
            </div>

            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Cập nhật
                </button>
                <a href="{{ route('media-folders.index') }}" class="ml-4 text-gray-600 hover:underline">⬅ Quay lại</a>
            </div>
        </form>
    </div>
@endsection
