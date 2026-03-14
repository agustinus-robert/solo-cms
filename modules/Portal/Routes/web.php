<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PortalMiddleware;

Route::middleware(['auth', \Modules\Portal\Http\Middleware\ShopMiddleware::class])->group(function () {
    Route::resource('dashboard', 'HomeController');

    Route::prefix('outlet')->namespace('Outlet')->name('outlet.')->group(function () {
        Route::resource('manage-outlet', 'ManageController')->parameters(['manages' => 'manage']);
        Route::get('datatable_outlet', 'ManageController@outletTable')->name('outlet.datatables');
    });

});
