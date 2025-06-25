<form action="#" method="POST" class="space-y-4 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @csrf
    @method('POST')

    {{-- Chọn thư mục cha / chứa --}}
    <div class="py-2 space-y-2">
        {!! \App\Helpers\MediaFolderHelper::renderFolderOptions() !!}
    </div>

    @if ($typeFilter === 'file')
        {{-- Tên Media --}}
        <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">🖼️ Tên Media</label>
        <input type="text" name="user_upload"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               placeholder="Người upload"
               value="{{ old('user_upload') }}">
    </div>

        {{-- Tag --}}
        <div class="py-2 space-y-2">
            <label class="block font-semibold mb-1">🏷️ Tags</label>
            <input type="text" name="tags[]"
                   class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                   placeholder="tags"
                   value="{{ old('tags[]') }}">
        </div>
    @endif

    {{-- Người upload --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">👤 Người upload</label>
        <input type="text" name="user_upload"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               placeholder="Người upload"
               value="{{ old('user_upload') }}">
    </div>

    {{-- Từ ngày --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">🕒 Từ ngày</label>
        <input type="datetime-local" name="start_date"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               value="{{ old('start_date') }}">
    </div>

    {{-- Đến ngày --}}
    <div class="py-2 space-y-2">
        <label class="block font-semibold mb-1">🕒 Đến ngày</label>
        <input type="datetime-local" name="end_date"
               class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
               value="{{ old('end_date', format_date(Carbon\Carbon::now())) }}">
    </div>
</form>
