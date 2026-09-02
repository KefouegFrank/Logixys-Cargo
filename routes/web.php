<?php

use App\Http\Controllers\TrackingController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/'.config('app.locale'));

Route::prefix('{locale}')
    ->where(['locale' => SetLocale::routePattern()])
    ->middleware('locale')
    ->group(function () {
        // Placeholders so the menu resolves; real content lands with each page's chunk.
        Route::view('/', 'pages.home')->name('home');
        Route::view('a-propos', 'pages.about')->name('about');
        Route::view('services', 'pages.services')->name('services');
        Route::view('contact', 'pages.contact')->name('contact');

        Route::get('suivi', [TrackingController::class, 'index'])->name('tracking.index');
        Route::get('suivi/{number}', [TrackingController::class, 'show'])
            ->name('tracking.show')
            ->middleware('throttle:10,1');
    });
