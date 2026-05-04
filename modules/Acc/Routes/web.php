<?php

use Illuminate\Support\Facades\Route;
use Modules\HRMS\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard-accounting', 'DashboardController@index')->name('dashboard');

    Route::namespace('Master')->group(function() {
        Route::resource('coa', 'CoaController');
    });
});
