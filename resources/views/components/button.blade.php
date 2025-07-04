@props([
    'href' => null,
    'onclick' => null,
    'type' => 'button',
    'target' => null,
    'nameBtn' => '',
    'class' => '',
])

@php
    $isLink = filled($href);
    $tag = $isLink ? 'a' : 'button';
    $finalHref = $href ?? 'javascript:void(0)';
@endphp

<{{ $tag }}
    @if($id) id="{{ $id }}" @endif
    @if ($isLink) href="{{ $finalHref }}" @else type="{{ $type }}" @endif
    @if ($onclick) onclick="{{ $onclick }}" @endif
    @if ($target) target="{{ $target }}" @endif
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
