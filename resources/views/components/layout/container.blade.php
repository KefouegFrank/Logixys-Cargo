@props(['size' => 'page', 'padded' => true])

{{--
    The one place the site's content width lives. Coloured bands stay full-bleed
    and wrap this, so only the content aligns. Set :padded="false" when children
    supply their own gutters, as the nav bar's logo block does.
--}}
<div
    {{ $attributes->class([
        'mx-auto w-full',
        'px-4 sm:px-6 lg:px-8' => $padded,
        'max-w-page' => $size === 'page',
        'max-w-prose' => $size === 'prose',
    ]) }}
>
    {{ $slot }}
</div>
