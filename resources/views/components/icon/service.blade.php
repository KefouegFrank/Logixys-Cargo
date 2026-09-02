@props(['name'])

{{--
    Hand-drawn as a matched set at Heroicons' 24×24 / 1.5-stroke spec, because
    Heroicons has no ship or warehouse glyph and mixing sources shows.
--}}
<svg
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.5"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    {{ $attributes }}
>
    @switch($name)
        @case('plane')
            <path d="M10.5 19.5 12 21l1.5-1.5M12 21v-3.5" />
            <path d="M2.5 13.2 12 3l9.5 10.2-3.1-.7-2.2 3.6-4.2-1-4.2 1-2.2-3.6-3.1.7Z" />
            @break

        @case('ship')
            <path d="M3 17.5c1.5 0 1.5 1.5 3 1.5s1.5-1.5 3-1.5 1.5 1.5 3 1.5 1.5-1.5 3-1.5 1.5 1.5 3 1.5 1.5-1.5 3-1.5" />
            <path d="M4.5 14.5 6 9.5h12l1.5 5" />
            <path d="M12 9.5V5.5M9.5 5.5h5" />
            <path d="M5.5 14.5h13" />
            @break

        @case('truck')
            <path d="M2.5 6.5h10.5v9H2.5z" />
            <path d="M13 9.5h4l3.5 3.5v2.5H13z" />
            <circle cx="7" cy="17.5" r="1.75" />
            <circle cx="17" cy="17.5" r="1.75" />
            @break

        @case('warehouse')
            <path d="M3 10 12 4.5 21 10v9.5H3z" />
            <path d="M8 19.5v-6h8v6" />
            <path d="M8 16.5h8" />
            @break
    @endswitch
</svg>
