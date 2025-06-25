<div id="{{ $id }}" class="rounded-2xl shadow-md bg-white p-6 {{ $class }}">
    <h1 class="text-xl font-bold mb-4 text-gray-800">{{ $title }}</h1>

    <div class="text-gray-700">
        {{ $slot }}
    </div>
</div>
