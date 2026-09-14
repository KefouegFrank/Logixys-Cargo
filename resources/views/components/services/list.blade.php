@php
    $services = config('service_cards');
@endphp

{{-- Alternating full-width bands, not a grid: this is the page's full detail on
     each service, distinct from the compact teaser grid on the home page. --}}
@foreach ($services as $i => $service)
    <section class="{{ $i % 2 === 1 ? 'bg-surface-sunken' : 'bg-white' }} py-16 lg:py-20">
        <x-layout.container>
            <x-services.detail-row :service="$service" :index="$i" />
        </x-layout.container>
    </section>
@endforeach
