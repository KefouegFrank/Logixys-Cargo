@extends('layouts.public')

@section('title', __('nav.home').' - '.config('app.name'))

@section('content')
    {{-- Placeholder. Real content lands with this page's own chunk. --}}
    <div class="mx-auto max-w-prose py-16 text-center">
        <h1 class="font-heading text-3xl font-extrabold text-ink">{{ __('nav.home') }}</h1>
        <p class="mt-3 text-ink-muted">{{ __('nav.home') }} — {{ app()->getLocale() }}</p>
    </div>
@endsection
