@extends('layouts.public')

@section('title', __('tracking.title'))
@section('robots', 'noindex')

@section('content')
    <x-layout.container class="py-10">
        <div class="mx-auto max-w-md text-center">
            <h1 class="font-heading text-2xl font-bold text-ink">{{ __('tracking.form_heading') }}</h1>

            <form method="GET" action="{{ route('tracking.index', app()->getLocale()) }}" class="mt-6 flex gap-2">
                <label for="number" class="sr-only">{{ __('tracking.form_label') }}</label>
                <input
                    type="text"
                    id="number"
                    name="number"
                    placeholder="{{ __('tracking.form_placeholder') }}"
                    required
                    class="w-full rounded-field border border-line px-4 py-2 transition-colors duration-200 focus:border-focus focus:outline-none"
                >
                <x-ui.button type="submit" class="shrink-0">
                    {{ __('tracking.form_submit') }}
                </x-ui.button>
            </form>
        </div>
    </x-layout.container>
@endsection
