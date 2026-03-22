<?php

use Illuminate\Support\Facades\Route;
use Modules\Web\Http\Controllers\EditorController; // Pastikan path-nya bener
use Modules\Web\Http\Controllers\MainController;
use Modules\Web\Http\Controllers\Electro\CartController;


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

    Route::get('cart/detail', [CartController::class, 'detail'])->name('web.cart.detail');
    Route::post('cart/add', [MainController::class, 'call'])->defaults('controller', 'cart')->defaults('method', 'add')->name('web.cart.add');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('web.cart.remove');

    Route::post('wishlist/toggle', [MainController::class, 'call'])->defaults('controller', 'wishlist')->defaults('method', 'toggle')->name('web.whistlist.add');

    mapMain('shop', 'shop', 'index', 'web.shop');
    mapMain('shop/detail/{param}', 'shop', 'show', 'web.shop.show');
    mapMain('contact', 'contact', 'index', 'web.contact');
    mapMain('cart/render-dropdown', 'cart', 'renderDropdown', 'web.cart.render');
    mapMain('wishlist/render-corner', 'wishlist', 'renderCorner', 'web.wishlist.render');


    Route::group([
        'middleware' => ['auth'],
        'prefix' => 'area',
        'as' => 'area.',
        'namespace' => 'Electro\Profile'
    ], function () {
        Route::resource('customer', 'CustomerController');
        Route::resource('address', 'AddressController');
    });


    Route::any('{controller?}/{method?}/{param?}', [MainController::class, 'call'])
        ->name('web.page');
});
