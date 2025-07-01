<div id="tabbed-folder-editor">
    {{-- Tabs Button --}}
    <div class="border-b border-gray-200 mb-4">
        <nav class="flex space-x-2">
            <button type="button" data-tab="breadcrumb" class="tab-btn">
                <span class="icon">🧭</span> Breadcrumb
            </button>
            <button type="button" data-tab="select" class="tab-btn">
                <span class="icon">📂</span> Chọn thư mục
            </button>
            <button type="button" data-tab="area" class="tab-btn">
                <span class="icon">🖱️</span> Kéo thả
            </button>
        </nav>
    </div>

    {{-- Hidden input để lưu tab đang chọn --}}
    <input type="hidden" name="active_tab" id="active-tab-input" value="{{ old('active_tab', 'breadcrumb') }}">

    {{-- Tab Content --}}
    <div class="tab-wrapper relative">
        {{-- Breadcrumb Tab --}}
        <div class="tab-content transition-tab" id="tab-breadcrumb">
            <div class="py-2 space-y-2">
                <p class="text-sm text-gray-500">Chú ý: Thao tác update sẽ luôn bắt đầu từ folder đang chọn</p>
            </div>

            @if ($folderName)
                <!-- 🎯 Button Box: Chọn thao tác -->
                <div class="py-2 space-y-2">
                    <label class="block font-semibold text-sm text-gray-700">🎯 Chọn thao tác</label>

                    <div class="grid grid-cols-4 gap-4">
                        @php($selected = old('action', 'add'))
                        @foreach(['add' => 'Add', 'rename' => 'Rename', 'move' => 'Move', 'rename_move' => 'Rename + Move'] as $value => $label)
                            <label for="action_{{ $value }}" class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-blue-50">
                                <input type="radio" name="action" id="action_{{ $value }}" value="{{ $value }}" class="text-blue-600" @checked($selected === $value)>
                                <span class="ml-1">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- 🔎 Ghi chú động -->
                    <div id="action-note" class="text-sm text-gray-600 mt-2">
                        Nhập đường dẫn để cấp thư mục mới từ thư mục hiện tại (có thể tạo nhiều cấp). <br />
                        Thư mục được tạo sẽ là thư mục con của thư mục hiện tại.
                    </div>
                </div>
            @endif

            <!-- 🧭 Nhập đường dẫn -->
            <div class="py-2 space-y-2">
                <label for="breadcrumb_path" class="block font-semibold text-sm text-gray-700">📝 Đường dẫn Breadcrumb</label>
                <input type="text"
                       name="breadcrumb_path"
                       id="breadcrumb_path"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                       placeholder="Ví dụ: Admin/Cosplay/HSR/Firefly"
                       value="{{ old('breadcrumb_path', $folderName) }}">
                <p class="text-sm text-gray-500">Nhập đường dẫn để tạo, đổi tên hoặc di chuyển thư mục.</p>
            </div>
        </div>

        {{-- Select Tab --}}
        <div class="tab-content transition-tab hidden" id="tab-select">
            <div class="py-2 space-y-2">
                {!! $renderFolderOptions !!}
                <p class="text-sm text-gray-500">Chọn thư mục cha mới nếu không nhập theo dạng breadcrumb.</p>
            </div>

            <div class="py-2 space-y-2">
                <label class="block font-semibold mb-1">📝 Tên Thư mục</label>
                <input type="text" name="folder_name"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                       placeholder="Tên folder"
                       value="{{ old('folder_name', $folderName ?? '') }}">
            </div>
        </div>

        {{-- Area Tab --}}
        <div class="tab-content transition-tab hidden" id="tab-area">
            <label class="block font-semibold">🖱️ Vị trí kéo thả thư mục</label>
            <div class="h-64 mt-6 text-center p-3 rounded-lg border-2 border-dashed border-gray-300 text-gray-500 hover:border-green-500 hover:text-green-600 transition bg-gray-50">
                Kéo và thả vào đây (mô phỏng – cần nâng cấp JS sau)
            </div>
        </div>
    </div>
</div>

