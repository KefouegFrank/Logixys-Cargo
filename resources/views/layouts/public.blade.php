<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Logixys Cargo')</title>
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @endif
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-white font-sans text-brand-navy antialiased">
    <header class="border-b border-brand-gray">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <a href="{{ url('/'.app()->getLocale()) }}" class="flex items-center gap-2">
                <span class="h-7 w-7">{!! file_get_contents(resource_path('images/logo-mark.svg')) !!}</span>
                <span class="font-heading text-lg font-bold text-brand-navy">Logixys Cargo</span>
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10">
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-brand-gray py-8">
        <div class="mx-auto max-w-5xl px-4 text-sm text-gray-500">
            &copy; {{ now()->year }} Logixys Cargo
        </div>
    </footer>
</body>
</html>
