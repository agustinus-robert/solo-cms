<?php

use Illuminate\Support\Facades\Route;
use Modules\Web\Http\Controllers\EditorController; // Pastikan path-nya bener
use Modules\Web\Http\Controllers\MainController;

function mapMain($path, $controller, $method, $name = null) {
    return Route::get($path, [MainController::class, 'call'])
        ->defaults('controller', $controller)
        ->defaults('method', $method)
        ->name($name ?? "web.$controller.$method");
}
Route::group(['middleware' => ['web']], function () {

    Route::resource('editor-sidebar', EditorController::class)->only([
        'edit', 'update'
    ]);

    mapMain('shop', 'shop', 'index', 'web.shop');
    mapMain('shop/detail/{param}', 'shop', 'show', 'web.shop.show');
    mapMain('contact', 'contact', 'index', 'web.contact');

    Route::any('{controller?}/{method?}/{param?}', [MainController::class, 'call'])
        ->name('web.page');
});
