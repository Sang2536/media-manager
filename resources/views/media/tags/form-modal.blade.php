<div class="fixed inset-0 z-50 overflow-y-auto bg-black/40 px-4 py-6 sm:px-0" id="showFolderModal" role="dialog" aria-modal="true" aria-labelledby="dialog-title">
    <div class="flex min-h-full items-center justify-center sm:p-0">
        <form id="tag-form" action="{{ $route }}" method="POST" class="relative w-full max-w-lg bg-white rounded-xl shadow-md">
            @csrf
            @php($method = $action === 'create' ? 'POST' : 'PUT')
            @method($method)

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h2 class="text-lg font-semibold text-gray-900 truncate">
                    {{ $action === 'create' ? '➕ Thêm thẻ mới' : '✏️ Cập nhật thẻ' }}
                </h2>
                <x-button
                    type="button"
                    name-btn="×"
                    class="text-gray-400 hover:text-red-500 text-2xl leading-none font-bold"
                    onclick="closeModal()"
                />
            </div>

            {{-- Body --}}
            <div class="px-5 py-4 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label for="names" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $action === 'create'
                            ? '📝 Tên các thẻ (phân cách bằng dấu phẩy)'
                            : '📝 Tên thẻ'
                        }}
                    </label>
                    <input
                        type="text"
                        id="{{ $action === 'create' ? 'names' : 'name' }}"
                        name="{{ $action === 'create' ? 'names' : 'name' }}"
                        value="{{ old($action === 'create' ? 'names' : 'name', $tag['name'] ?? '') }}"
                        placeholder="{{ $action === 'create' ? 'VD: anime, cosplay, yuri' : 'VD: anime' }}"
                        class="w-full rounded-md border px-4 py-2
                        @error($action === 'create' ? 'names' : 'name') border-red-500 @else border-gray-300 @enderror
                            focus:outline-none focus:ring-2 focus:ring-blue-400"
                    >
                    @error($action === 'create' ? 'names' : 'name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">🎨 Màu thẻ</label>
                    <input
                        type="color"
                        id="color"
                        name="color"
                        value="{{ old('color', $tag['color'] ?? '#FFFFFF') }}"
                        class="h-10 w-20 cursor-pointer rounded border border-gray-300"
                    >
                    @error('color')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end items-center gap-3 px-5 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <x-button
                    type="submit"
                    name-btn="💾 Lưu"
                    class="bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm px-4 py-2 rounded-md shadow-sm"
                />

                <x-button
                    type="button"
                    name-btn="Huỷ"
                    onclick="closeModal()"
                    class="bg-white hover:bg-gray-100 text-gray-700 font-semibold text-sm px-4 py-2 rounded-md border border-gray-300"
                />
            </div>
        </form>
    </div>
</div>
