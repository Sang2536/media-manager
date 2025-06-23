<table class="w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50 text-gray-700">
        <tr>
            @foreach ($headers as $header)
                <th class="px-6 py-3 font-semibold">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
