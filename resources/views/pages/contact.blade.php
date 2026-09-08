@php
    use App\Enums\ContactSubject;
    use App\Http\Requests\ContactRequest;

    $locale = app()->getLocale();
    $contact = config('brand.contact');
@endphp

@extends('layouts.public')

@section('title', __('nav.contact').' - '.config('app.name'))
@section('description', __('contact.intro'))

@section('content')
    <x-layout.page-header :title="__('nav.contact')" />

    <x-layout.container class="py-12 lg:py-16">
        {{-- The card straddles two surfaces: white for the form, navy for the details.
             Gold stays on the rules, the icon chips and the submit button so the brand
             reads as an accent rather than a wash of colour. --}}
        {{-- The reference card: form on the left, a solid contact panel on the right,
             flush together. Its orange becomes navy so the colour carries the brand
             instead of shouting; gold stays on the rules, one icon tile and the button. --}}
        <div id="contact-form" class="mx-auto max-w-5xl overflow-hidden rounded-card shadow-card lg:grid lg:grid-cols-12">
            <div class="bg-white p-6 sm:p-9 lg:col-span-7">
                <h2 class="text-center font-heading text-2xl font-extrabold uppercase tracking-wide text-ink">
                    {{ __('contact.heading') }}
                </h2>
                <div class="mt-4 h-0.5 w-full bg-accent" aria-hidden="true"></div>

                @if (session('contact.sent'))
                    <div role="status" class="mt-6 flex gap-3 rounded-card border border-success-border bg-success-bg p-4">
                        <x-heroicon-o-check-circle class="h-5 w-5 shrink-0 text-success-fg" aria-hidden="true" />
                        <div class="text-sm">
                            <p class="font-semibold text-success-fg">{{ __('contact.success_title') }}</p>
                            <p class="mt-0.5 text-success-fg/90">{{ __('contact.success_body') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-card border border-danger-border bg-danger-bg p-4 text-sm">
                        <p class="font-semibold text-danger-fg">{{ __('contact.error_title') }}</p>
                        <ul class="mt-1.5 list-inside list-disc text-danger-fg/90">
                            @foreach ($errors->unique() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store', ['locale' => $locale]) }}" class="mt-7 space-y-4">
                    @csrf

                    {{-- Bots fill every field they find; people never see this one. --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="contact-{{ ContactRequest::HONEYPOT }}">{{ ContactRequest::HONEYPOT }}</label>
                        <input
                            id="contact-{{ ContactRequest::HONEYPOT }}"
                            type="text" name="{{ ContactRequest::HONEYPOT }}" value="" tabindex="-1" autocomplete="off"
                        >
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-contact.field
                            name="name" :label="__('contact.form.name')" required
                            icon="heroicon-o-user" autocomplete="name"
                        />
                        <x-contact.field
                            name="email" type="email" :label="__('contact.form.email')" required
                            icon="heroicon-o-envelope" autocomplete="email"
                        />
                        <x-contact.field
                            name="phone" type="tel" :label="__('contact.form.phone')"
                            icon="heroicon-o-device-phone-mobile" autocomplete="tel"
                        />
                        <x-contact.field
                            name="subject" type="select" :label="__('contact.form.subject')" required
                            icon="heroicon-o-document-text" :options="ContactSubject::options()"
                        />
                    </div>

                    <x-contact.field
                        name="message" type="textarea" :label="__('contact.form.message')" required
                        icon="heroicon-o-pencil"
                    />

                    <div class="pt-2 text-center">
                        <x-ui.button type="submit" variant="accent" size="md" class="w-full uppercase tracking-wide sm:w-auto">
                            {{ __('contact.form.submit') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>

            <div class="bg-navy-900 p-6 sm:p-9 lg:col-span-5">
                <h2 class="font-heading text-lg font-extrabold uppercase tracking-wide text-white">
                    {{ __('contact.info_heading') }}
                </h2>
                <div class="mt-4 h-0.5 w-full bg-white/30" aria-hidden="true"></div>

                <div class="mt-6 space-y-5">
                    <x-contact.detail icon="heroicon-s-phone" tone="accent" :label="__('contact.info_phone')">
                        <p>
                            <a href="tel:{{ $contact['phone_href'] }}" class="transition-colors duration-200 hover:text-navy-900">
                                {{ $contact['phone'] }}
                            </a>
                        </p>
                    </x-contact.detail>

                    <x-contact.detail icon="heroicon-s-envelope" tone="navy" :label="__('contact.info_email')">
                        <p>
                            <a href="mailto:{{ $contact['email'] }}" class="break-all transition-colors duration-200 hover:text-navy-900">
                                {{ $contact['email'] }}
                            </a>
                        </p>
                    </x-contact.detail>

                    <x-contact.detail icon="heroicon-s-map-pin" tone="muted" :label="__('contact.info_address')">
                        <p>{{ $contact['address'] }}</p>
                        <p>{{ $contact['hours_weekday'] }}</p>
                        <p>{{ $contact['hours_weekend'] }}</p>
                    </x-contact.detail>
                </div>
            </div>
        </div>
    </x-layout.container>

    @if (filled($contact['map_lat']) && filled($contact['map_lng']))
        <section aria-label="{{ __('contact.map_heading') }}" class="print:hidden">
            <x-shipment-map
                variant="office"
                :position="['lat' => (float) $contact['map_lat'], 'lng' => (float) $contact['map_lng']]"
                :location-label="$contact['address']"
                :popup-title="config('app.name')"
                height="clamp(300px, 45vh, 440px)"
            />
        </section>

        @push('head')
            <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
            <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
        @endpush
    @endif
@endsection
