@php($tag = $tag)

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
