<?php

use Illuminate\Support\Facades\Route;
use Modules\Web\Http\Controllers\EditorController;
use Modules\Web\Http\Controllers\MainController;
use Modules\Web\Http\Controllers\Electro\CartController;
use Modules\Web\Http\Controllers\Electro\ProductController;


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
    Route::post('cart/check-stock', [CartController::class, 'checkStock'])->name('web.cart.check-stock');
    Route::post('cart/add', [MainController::class, 'call'])->defaults('controller', 'cart')->defaults('method', 'add')->name('web.cart.add');
    Route::post('cart/add-on-detail', [MainController::class, 'call'])->defaults('controller', 'cart')->defaults('method', 'addOnDetail')->name('web.cart.add-on-detail');

    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('web.cart.remove');
    Route::get('product/detail/{id}', [ProductController::class, 'show'])->name('web.shop.show');

    mapMain('shop', 'shop', 'index', 'web.shop');
    mapMain('contact', 'contact', 'index', 'web.contact');
    mapMain('cart/render-dropdown', 'cart', 'renderDropdown', 'web.cart.render');

    Route::group([
        'middleware' => ['auth'],
        'prefix' => 'area',
        'as' => 'area.',
        'namespace' => 'Electro\Profile'
    ], function () {
        Route::resource('customer', 'CustomerController');
        Route::resource('address', 'AddressController');
        Route::resource('checkout', 'CheckoutController')->only('index', 'store');
        Route::get('finish/{reference}', 'FinishController@index')->name('finish.index');
        Route::get('wishlist', 'WishlistController@index')->name('wishlist.index');
        Route::post('wishlist/toggle', 'WishlistController@toggle')->name('wishlist.toggle');
        Route::get('wishlist/render-corner', 'WishlistController@getWishlistCount')->name('wishlist.render');
    });


    Route::any('{controller?}/{method?}/{param?}', [MainController::class, 'call'])
        ->name('web.page');
});
