<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/fr');

Route::prefix('{locale}')->where(['locale' => 'fr|en'])->middleware('locale')->group(function () {
    Route::get('suivi', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('suivi/{number}', [TrackingController::class, 'show'])
        ->name('tracking.show')
        ->middleware('throttle:10,1');
});
