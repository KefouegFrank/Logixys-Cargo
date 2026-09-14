@extends('layouts.public')

@section('title', __('legal.privacy.title').' — '.config('app.name'))
@section('robots', 'noindex')

@php
    $t = __('legal_privacy');
    $name = config('company.legal_name') ?? config('app.name');
    $email = config('brand.contact.email');
    $locale = app()->getLocale();

    $links = [
        ':notice_link:' => '<a href="'.route('legal.notice', ['locale' => $locale]).'">'.$t['link_notice'].'</a>',
        ':email_link:' => '<a href="mailto:'.$email.'">'.$email.'</a>',
        ':cnil_link:' => '<a href="https://www.cnil.fr/fr/plaintes" target="_blank" rel="noopener">CNIL</a>',
        ':hours:' => (string) (int) ceil((int) config('session.lifetime') / 60),
        ':name:' => e($name),
    ];
@endphp

@section('content')
    <x-layout.page-header :title="__('legal.privacy.title')" />

    <x-layout.container class="py-16">
        <p class="text-sm text-ink-muted">{{ __('legal.updated_at', ['date' => now()->translatedFormat('d F Y')]) }}</p>

        <p class="mt-4 max-w-3xl text-sm leading-relaxed text-ink-muted">
            {!! strtr($t['intro'], $links) !!}
        </p>

        <div class="mt-8 grid gap-x-12 gap-y-10 lg:grid-cols-[16rem_1fr] lg:items-start">
            <x-legal.toc :items="$t['sections']" />

            <div class="max-w-3xl">
                @foreach ($t['articles'] as $number => $body)
                    <x-legal.article :number="$number" :title="$t['sections'][$number]">
                        {!! strtr($body, $links) !!}
                    </x-legal.article>

                    @if ($number === 3)
                        <div class="mt-2 overflow-x-auto rounded-card border border-line">
                            <table class="w-full min-w-[36rem] text-left text-sm">
                                <thead>
                                    <tr class="bg-surface-sunken">
                                        @foreach ($t['table']['headers'] as $header)
                                            <th class="border-b border-line px-4 py-2.5 font-semibold text-ink">{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach ($t['table']['rows'] as $row)
                                        <tr>
                                            @foreach ($row as $cell)
                                                <td class="px-4 py-2.5 align-top">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </x-layout.container>
@endsection
