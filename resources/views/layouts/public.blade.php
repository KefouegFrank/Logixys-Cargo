<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @hasSection('description')
        <meta name="description" content="@yield('description')">
    @endif
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @endif

    @foreach (\App\Support\LocaleSwitcher::options() as $alternate)
        <link rel="alternate" hreflang="{{ $alternate['code'] }}" href="{{ $alternate['url'] }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ \App\Support\LocaleSwitcher::options()[0]['url'] }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {{-- Blocking on purpose: this has to settle before the first paint, or the
         page flashes at the top before the previous scroll position is put back. --}}
    <script>
        (function () {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            try {
                if (sessionStorage.getItem('logixys:locale-scroll') === null) return;
            } catch (e) {
                return;
            }

            var root = document.documentElement;
            root.classList.add('is-switching-locale');
            // Never strand the page hidden if the module fails to load.
            setTimeout(function () { root.classList.remove('is-switching-locale'); }, 800);
        })();
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-surface font-sans text-ink antialiased">
    <a
        href="#main"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-field focus:bg-navy-900 focus:px-4 focus:py-2 focus:text-white"
    >{{ __('nav.skip_to_content') }}</a>

    <x-layout.header />

    {{-- Unconstrained on purpose: pages mix full-bleed bands (the hero) with
         contained ones, so each section wraps itself in x-layout.container. --}}
    <main id="main">
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-line py-8">
        <x-layout.container class="text-sm text-ink-subtle">
            &copy; {{ now()->year }} {{ config('app.name') }}
        </x-layout.container>
    </footer>
</body>
</html>
