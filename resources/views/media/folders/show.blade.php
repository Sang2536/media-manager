<div class="relative z-10" id="showFolderModal" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <button onclick="closeModal()"
                        class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h2 class="text-xl font-bold mb-4">📁 Thông tin Thư mục</h2>

                            {{-- Breadcrumb --}}
                            @if ($breadcrumbs)
                                <x-breadcrumb
                                    :breadcrumbs="$breadcrumbs"
                                    :view-mode="$view"
                                    :route-action="[
                                        'index' => route('media-folders.index')
                                    ]"
                                    :current="$folder->name"
                                />
                            @endif

                            <div class="space-y-2 text-gray-700">
                                <div class="my-2">👤 {{ $folder->user->name }}</div>
                                <div><strong>Tên:</strong> {{ $folder->name }}</div>
                                <div><strong>ID:</strong> {{ $folder->id }}</div>
                                <div><strong>Folder con:</strong> {{ $folder->children_count ?? 0 }}</div>
                                <div><strong>Ảnh:</strong> {{ $folder->files_count ?? 0 }}</div>
                                <div><strong>Ngày tạo:</strong> {{ $folder->created_at->format('d/m/Y H:i') }}</div>
                                <div><strong>Lần sửa gần nhất:</strong> {{ $folder->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <a href="{{ route('media-folders.edit', $folder) }}" type="button" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 sm:ml-3 sm:w-auto">Edit</a>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal()"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
