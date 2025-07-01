@extends('layouts.app')

@section('title', 'Guide')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            🗓️ Logs
        </h1>
        <p class="text-gray-500">Ghi lại tất cả hoạt động của người dùng.</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-xl overflow-hidden">
            <x-table
                :headers="['ID', 'User', 'Active', 'Target', 'Description', 'Created at']"
            >
                {{-- Table body --}}
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-100 transition cursor-pointer">
                        <td class="px-6 py-4">{{ $log->id }}</td>
                        <td class="flex px-6 py-4">👤 {{ $folder->user->name }}</td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <span>{{ $log->active }}</span>
                        </td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <span>{{ $log->target_type }}</span>
                            @if ($log->target_id)
                                <span>{{ $log->target_id }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $log->description }}</td>
                        <td class="px-6 py-4">{{ $folder->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có thư mục nào.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </div>
@endsection
