@extends('layouts.public')

@section('title', __('nav.services').' - '.config('app.name'))

@section('content')
    <x-layout.container class="py-10">
        {{-- Placeholder. Real content lands with this page's own chunk. --}}
        <div class="mx-auto max-w-prose py-16 text-center">
            <h1 class="font-heading text-3xl font-extrabold text-ink">{{ __('nav.services') }}</h1>
            <p class="mt-3 text-ink-muted">{{ __('nav.services') }} — {{ app()->getLocale() }}</p>
        </div>
    </x-layout.container>
@endsection
