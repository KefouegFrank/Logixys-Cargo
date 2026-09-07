@extends('layouts.public')

@section('title', __('nav.contact').' - '.config('app.name'))

@section('content')
    <x-layout.page-header :title="__('nav.contact')" />

    <x-layout.container class="py-10">
        {{-- Placeholder. Real content lands with this page's own chunk. --}}
        <div class="mx-auto max-w-prose py-16 text-center">
            <p class="text-ink-muted">{{ __('nav.contact') }} — {{ app()->getLocale() }}</p>
        </div>
    </x-layout.container>
@endsection
