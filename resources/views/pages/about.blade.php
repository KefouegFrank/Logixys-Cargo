@extends('layouts.public')

@section('title', __('nav.about').' - '.config('app.name'))

@section('content')
    <x-layout.page-header :title="__('nav.about')" />

    <x-about.intro />

    <x-about.capabilities />

    <x-why-choose.section />
@endsection
