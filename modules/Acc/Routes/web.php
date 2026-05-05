<?php

use Illuminate\Support\Facades\Route;
use Modules\HRMS\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard-accounting', 'DashboardController@index')->name('dashboard');

    Route::namespace('Transaction')->group(function() {
        Route::resource('ledger', 'LedgerController');
    });

    Route::namespace('Reporting')->group(function(){
        Route::get('trial-balance', 'TrialBalanceController@index')->name('trial-balance');
        Route::get('profit-loss', 'ProfitLossController@index')->name('profit-loss');
    });

    Route::namespace('Master')->group(function() {
        Route::resource('coa', 'CoaController');
        Route::resource('period', 'PeriodController');
        Route::resource('beginning-balance', 'BeginningBalanceController');
    });
});
