@extends('layouts.public')

@section('title', __('legal.terms.title').' — '.config('app.name'))
@section('robots', 'noindex')

@php
    $t = __('legal_terms');
    $name = config('app.name');
    $locale = app()->getLocale();

    $links = [
        ':privacy_link:' => '<a href="'.route('legal.privacy', ['locale' => $locale]).'">'.$t['link_privacy'].'</a>',
        ':name:' => e($name),
    ];
@endphp

@section('content')
    <x-layout.page-header :title="__('legal.terms.title')" />

    <x-layout.container class="py-16">
        <p class="text-sm text-ink-muted">{{ __('legal.updated_at', ['date' => now()->translatedFormat('d F Y')]) }}</p>

        <div class="mt-8 grid gap-x-12 gap-y-10 lg:grid-cols-[16rem_1fr] lg:items-start">
            <x-legal.toc :items="$t['sections']" />

            <div class="max-w-3xl">
                @foreach ($t['articles'] as $number => $body)
                    <x-legal.article :number="$number" :title="$t['sections'][$number]">
                        {!! strtr($body, $links) !!}
                    </x-legal.article>
                @endforeach
            </div>
        </div>
    </x-layout.container>
@endsection
