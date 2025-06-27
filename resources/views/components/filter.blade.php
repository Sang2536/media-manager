<form id={{ $formId }} action={{ $formAction }} method={{ $formMethod }}
    {{ $attributes->merge(['class' => 'space-y-4 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6']) }}>
    @csrf
    @if (strtolower($formMethod) === 'post'
        || strtolower($formMethod) === 'put'
        || strtolower($formMethod) === 'patch'
        || strtolower($formMethod) === 'delete'
    )
        @method($formMethod)
    @endif

    {{ $slot }}

    {{-- Chọn thư mục cha --}}
    <div class="py-2 space-y-2">
        {!! \App\Helpers\MediaFolderHelper::renderFolderOptions() !!}
    </div>

    @if ($typeFilter === 'file')
        {{-- Tag --}}
        <div class="py-2 space-y-2">
            <label class="block font-semibold mb-1">🏷️ Tags</label>
            <input type="text" name="tags[]"
                   class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                   placeholder="tags"
                   value="{{ $filters['tag'] ?? null }}">
        </div>
    @endif

    {{-- Tên --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">📝 Tên</label>
        <input type="text" name="name"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               placeholder="Tên"
               value="{{ $filters['name'] ?? null }}">
    </div>

    {{-- Người upload --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">👤 Người upload</label>
        <input type="text" name="user_upload"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               placeholder="Người upload"
               value="{{ $filters['user_upload'] ?? null }}">
    </div>

    {{-- Kho lưu trữ --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">🗃️ Kho lưu trữ</label>
        <input type="text" name="storage"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               placeholder="Kho lưu trữ"
               value="{{ $filters['storage'] ?? null }}">
    </div>

    {{-- Từ ngày --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">🕒 Từ ngày</label>
        <input type="datetime-local" name="start_date"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               value="{{ $filters['start_date'] ?? null }}">
    </div>

    {{-- Đến ngày --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">🕒 Đến ngày</label>
        <input type="datetime-local" name="end_date"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               value="{{ $filters['end_date'] ?? format_date(Carbon\Carbon::now()) }}">
    </div>
</form>
