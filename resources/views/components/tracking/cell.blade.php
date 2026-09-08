{{-- Table cell that carries its own column heading below md, where the rows render as
     stacked cards instead of a table nobody can read on a phone. --}}
@props(['label'])

<td {{ $attributes->class(['flex items-baseline justify-between gap-4 border-b border-line py-2 last:border-0 md:table-cell md:border-0 md:px-3 md:py-2.5']) }}>
    <span class="text-xs font-semibold uppercase tracking-wide text-ink-subtle md:hidden">{{ $label }}</span>
    <span class="text-right md:text-left">{{ $slot }}</span>
</td>
