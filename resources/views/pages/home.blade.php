@extends('layouts.public')

@section('title', config('app.name').' — '.__('nav.home'))
@section('description', __('hero.slides.air.body'))

@section('content')
    <x-hero.carousel />

    <x-services.grid />

    <x-about.section />
@endsection
