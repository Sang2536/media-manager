@extends('layouts.app')

@section('title', 'Logs')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            🗓️ Logs
        </h1>
        <p class="text-gray-500">Ghi lại tất cả hoạt động của người dùng trên hệ thống.</p>
    </div>

    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-end flex-wrap gap-4 mb-8">
            <x-button
                class="inline-flex items-center gap-2 border border-cyan-500 bg-cyan-500 text-white px-5 py-2 rounded-lg hover:bg-cyan-600 transition shadow"
                name-btn="🔄 Refresh"
                onclick="window.location.href = window.location.origin + window.location.pathname"
            />
            <x-button
                class="inline-flex items-center gap-2 border border-red-500 bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition shadow"
                name-btn="🗑️ Clear"
                onclick="clearAll('{{ route('media-logs.clear') }}', {
                    confirmText: 'Bạn chắc chắn muốn xoá tất cả logs chứ?',
                    successMessage: 'Đã xoá tất cả logs!',
                    onSuccess: () => location.reload()
                })"
            />
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-xl overflow-hidden">
            <x-table :headers="['ID', 'User', 'Action', 'Model', 'Description', 'Created at']">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 transition cursor-pointer border-b">
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $log->id }}</td>

                        <td class="px-6 py-4 text-sm text-gray-700 flex items-center gap-2">
                            <span>👤</span>
                            <span>{{ $log->user->name ?? 'N/A' }}</span>
                        </td>

                        <td class="px-6 py-4 text-sm bg">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $log->action_color['bg'] }} {{ $log->action_color['text'] }}">
                                <span>{{ $log->action_icon }}</span>
                                <span class="capitalize">{{ $log->action }}</span>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="font-semibold">{{ Str::headline($log->target_type) }}</span>
                            @if ($log->target_id)
                                <span class="text-gray-400">#{{ $log->target_id }}</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $log->description ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->diffForHumans() }}<br>
                            <span class="text-xs text-gray-400">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                            Không có log nào được ghi lại.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $logs->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
