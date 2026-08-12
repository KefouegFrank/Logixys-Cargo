@extends('layouts.public')

@section('title', __('tracking.title'))
@section('robots', 'noindex')

@section('content')
    <div class="mx-auto max-w-md text-center">
        <h1 class="font-heading text-2xl font-bold text-brand-navy">{{ __('tracking.form_heading') }}</h1>

        <form method="GET" action="{{ route('tracking.index', app()->getLocale()) }}" class="mt-6 flex gap-2">
            <label for="number" class="sr-only">{{ __('tracking.form_label') }}</label>
            <input
                type="text"
                id="number"
                name="number"
                placeholder="{{ __('tracking.form_placeholder') }}"
                required
                class="w-full rounded-md border border-brand-gray px-4 py-2 focus:border-brand-blue focus:outline-none"
            >
            <button type="submit" class="shrink-0 rounded-md bg-brand-gold px-5 py-2 font-semibold text-brand-navy">
                {{ __('tracking.form_submit') }}
            </button>
        </form>
    </div>
@endsection
