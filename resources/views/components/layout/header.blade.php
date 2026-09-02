{{-- `open` is shared by the hamburger in the nav bar and the drawer below it. --}}
<header x-data="{ open: false }" @keydown.escape.window="open = false" class="relative z-40">
    <x-layout.top-bar />
    <x-layout.nav-bar />
    <x-layout.mobile-menu />
</header>
