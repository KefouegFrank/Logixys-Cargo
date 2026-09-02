@props(['variant' => 'dark'])

{{-- 'light' recolours the navy to white, so it is the one to use on navy or any dark surface. --}}
<img
    src="{{ asset(config("brand.lockup.{$variant}")) }}"
    width="{{ config('brand.lockup.width') }}"
    height="{{ config('brand.lockup.height') }}"
    alt="{{ config('app.name') }}"
    {{ $attributes->class(['w-auto']) }}
>
