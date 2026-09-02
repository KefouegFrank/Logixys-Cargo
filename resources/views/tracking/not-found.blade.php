@extends('layouts.public')

@section('title', __('tracking.not_found_heading'))
@section('robots', 'noindex')

@section('content')
    <x-layout.container class="py-10">
        <div class="mx-auto max-w-md text-center">
            <h1 class="font-heading text-2xl font-bold text-ink">{{ __('tracking.not_found_heading') }}</h1>
            <p class="mt-3 text-ink-muted">{{ __('tracking.not_found_body') }}</p>
            <a href="{{ route('tracking.index', app()->getLocale()) }}" class="mt-6 inline-block text-navy-700 underline decoration-2 underline-offset-4 transition-colors duration-200 hover:text-ink">
                {{ __('tracking.form_submit') }}
            </a>
        </div>
    </x-layout.container>
@endsection
