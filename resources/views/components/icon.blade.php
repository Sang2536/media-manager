@switch($set)
    {{-- FontAwesome --}}
    @case('fa')
        <i class="fa fa-{{ $name }} {{ $class }}"></i>
        @break

    {{-- Heroicons - outline --}}
    @case('hero')
        <svg class="w-5 h-5 {{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <use href="#icon-{{ $name }}" />
        </svg>
        @break

    {{-- SVG inline (tuỳ chỉnh sau) --}}
    @default
        <svg class="w-5 h-5 {{ $class }}">
            <use href="#icon-{{ $name }}" />
        </svg>
@endswitch
