<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard-tour', 'DashboardController@index')->name('dashboard');

    Route::namespace('Transaction')->group(function () {
        Route::resource('booking', 'BookingController');
        Route::resource('package', 'PackageController');
        Route::get('package-show/{package}', 'PackageDetailController@show')->name('package.detail.show');
        Route::post('package-detail/{package}', 'PackageDetailController@store')->name('package.detail.store');
        Route::delete('package-detail-destroy/{package}', 'PackageDetailController@destroy')->name('package.detail.destroy');
        Route::post('package/detail/update-order', 'PackageDetailController@updateOrder')->name('package.detail.update-order');

        Route::get('tour/{tour}/photos', 'TourPhotoController@show')->name('photo.show');
        Route::post('tour/{tour}/photos', 'TourPhotoController@store')->name('photo.store');
        Route::post('tour/{tour}/photos/{photo}/primary', 'TourPhotoController@setPrimary')->name('photo.primary');
        Route::delete('photos/{photo}', 'TourPhotoController@destroy')->name('photo.destroy');

        Route::post('booking-order/store', 'BookingOrderController@store')->name('booking-order.store');
        Route::get('booking-order/{order_number}', 'BookingOrderController@show')->name('booking-order.show');

        Route::get('package-times/{package}', 'PackageTimeController@show')->name('package.times');
        Route::post('package-times/{package}', 'PackageTimeController@store')->name('package.times.store');
        Route::delete('package-times/{package}', 'PackageTimeController@destroy')->name('package.times.destroy');
    });



    Route::namespace('Master')->group(function () {
        Route::resource('label', 'LabelController');
        Route::resource('availability', 'AvailabilityController');
        Route::resource('location', 'LocationController');
    });
});
