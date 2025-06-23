<div class="relative z-10" id="showFileModal" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-5xl">
                <div class="bg-white p-6">
                    <h2 class="text-xl font-bold mb-4">📁 Thông tin Media</h2>

                    {{-- Breadcrumb --}}
                    @if ($breadcrumbs)
                        <nav class="mb-6 text-sm flex items-center flex-wrap text-gray-700">
                            <a href="{{ route('media-files.index', ['view' => $view]) }}" class="text-blue-600 hover:underline flex items-center gap-1">
                                Root
                            </a>

                            @foreach ($breadcrumbs as $crumb)
                                <span class="mx-2 text-gray-400">/</span>
                                <a href="{{ route('media-folders.index', ['parent' => $crumb->id, 'view' => $view]) }}"
                                   class="text-blue-600 hover:underline flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h4l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                    </svg>
                                    {{ $crumb->name }}
                                </a>
                            @endforeach
                        </nav>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Left: File Info --}}
                        <div>
                            <h2 class="text-xl font-bold mb-2 truncate">
                                {{ basename($file->filename) }}
                            </h2>

                            <p class="text-sm text-gray-500 mb-4">
                                📂 <strong>Thư mục:</strong> {{ $file->folder->name ?? 'Không có' }}<br>
                                👤 <strong>Người đăng:</strong> {{ $file->user->name }}
                            </p>

                            <div class="mt-2">
                                <h3 class="text-sm font-medium text-gray-700 mb-1">🏷 Tags:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @forelse ($file->tags as $tag)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $tag->name }}</span>
                                    @empty
                                        <span class="text-xs text-gray-400">Không có tag</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Right: Image + Copy --}}
                        <div>
                            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                                <img src="{{ asset($file->path) }}"
                                     alt="{{ $file->original_name }}"
                                     class="w-full h-64 object-cover" />
                            </div>

                            {{-- Copy URL --}}
                            <div class="mt-4 flex justify-between items-center text-xs border border-gray-300 bg-gray-50 rounded">
                                <div class="ml-2 truncate text-gray-700 w-4/6" title="{{ asset($file->path) }}">
                                    {{ asset($file->path) }}
                                </div>
                                <button onclick="copyToClipboard('{{ asset($file->path) }}')"
                                        class="ml-2 border border-gray-300 hover:bg-gray-300 font-semibold rounded-none px-2 py-2 text-xs">
                                    📋 Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <a href="{{ route('media-files.edit', $file) }}" type="button" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 sm:ml-3 sm:w-auto">Edit</a>
                    <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal()"
                    >
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
