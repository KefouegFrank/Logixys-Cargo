@extends('layouts.public')

@section('title', __('tracking.not_found_heading'))
@section('robots', 'noindex')

@section('content')
    <div class="mx-auto max-w-md text-center">
        <h1 class="font-heading text-2xl font-bold text-brand-navy">{{ __('tracking.not_found_heading') }}</h1>
        <p class="mt-3 text-gray-600">{{ __('tracking.not_found_body') }}</p>
        <a href="{{ route('tracking.index', app()->getLocale()) }}" class="mt-6 inline-block text-brand-blue underline">
            {{ __('tracking.form_submit') }}
        </a>
    </div>
@endsection
