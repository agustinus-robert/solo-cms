<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard-hotel', 'DashboardController@index')->name('dashboard');

    Route::namespace('Room')->group(function () {
        Route::resource('room', 'RoomController');
    });
});
