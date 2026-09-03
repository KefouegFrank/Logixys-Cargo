@extends('layouts.public')

@section('title', __('legal.privacy.title').' — '.config('app.name'))
@section('robots', 'noindex')

@section('content')
    <x-layout.container size="prose" class="py-16">
        <h1 class="font-heading text-3xl font-extrabold text-ink">{{ __('legal.privacy.title') }}</h1>

        {{-- Placeholder: the text is client-supplied and jurisdiction-dependent. --}}
        <p class="mt-6 rounded-card border border-warning-border bg-warning-bg px-5 py-4 text-sm text-warning-fg">
            {{ __('legal.privacy.placeholder') }}
        </p>
    </x-layout.container>
@endsection
