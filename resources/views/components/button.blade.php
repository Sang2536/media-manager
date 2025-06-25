@php
    $tag = $tag;

    $class = $class ?? 'mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto';
@endphp

<{{ $tag }}
    @if($id) id="{{ $id }}" @endif

    @if($tag === 'a')
        href="{{ $href }}"
    @else
        type="{{ $type }}"
    @endif

    {{ $attributes->merge(['class' => $class]) }}
>
    @isset($iconLeft)
        <x-icon :name="$iconLeft" class="mr-1" />
    @endisset

    @if($nameBtn)
        <span>{{ $nameBtn }}</span>
    @endif

    {{ $slot }}

    @isset($iconRight)
        <x-icon :name="$iconRight" class="ml-1" />
    @endisset
</{{ $tag }}>
