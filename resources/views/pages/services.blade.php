@extends('layouts.public')

@section('title', __('nav.services').' - '.config('app.name'))

@section('content')
    <x-layout.page-header :title="__('nav.services')" />

    <div class="bg-white py-16 lg:py-20">
        <x-layout.container>
            <div class="mx-auto max-w-2xl text-center">
                <x-ui.eyebrow class="mx-auto w-fit">{{ __('services.eyebrow') }}</x-ui.eyebrow>

                <h2 class="mt-5 font-heading text-3xl font-extrabold leading-tight text-ink sm:text-4xl">
                    {{ __('services.heading_before') }}
                    <span class="bg-[linear-gradient(to_top,var(--color-gold-400)_0.2em,transparent_0.2em)] px-0.5">{{ __('services.heading_highlight') }}</span>
                    {{ __('services.heading_after') }}
                </h2>

                <p class="mt-4 text-base leading-relaxed text-ink-muted">{{ __('services.intro') }}</p>
            </div>
        </x-layout.container>
    </div>

    <x-services.list />

    <x-services.faq />
@endsection
