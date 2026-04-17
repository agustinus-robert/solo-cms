<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
});
