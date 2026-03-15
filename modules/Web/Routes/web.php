<?php

use Illuminate\Support\Facades\Route;
use Modules\Web\Http\Controllers\EditorController; // Pastikan path-nya bener
use Modules\Web\Http\Controllers\MainController;

Route::group(['middleware' => ['web']], function () {

    Route::resource('editor-sidebar', EditorController::class)->only([
        'edit', 'update'
    ]);

    Route::any('{controller?}/{method?}/{param?}', [MainController::class, 'call'])
        ->name('web.page');
});
