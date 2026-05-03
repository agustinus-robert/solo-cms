<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard-tour', 'DashboardController@index')->name('dashboard');

    Route::namespace('Transaction')->group(function () {
        Route::resource('booking', 'BookingController');
    });

    Route::namespace('Master')->group(function () {
        Route::resource('label', 'LabelController');
        Route::resource('availability', 'AvailabilityController');
    });
});
