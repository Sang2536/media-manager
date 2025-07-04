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

    <div class="flex items-center gap-3">
        <x-button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition" name-btn="💾 Cập nhật" />

        <x-button type="button" onclick="clearForm('{{ $formId }}')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition" name-btn="🔙 Quay lại" />
    </div>
</form>
