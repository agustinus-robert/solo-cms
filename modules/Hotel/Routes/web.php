<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {
    Route::get('/dashboard-hotel', 'DashboardController@index')->name('dashboard');

    Route::namespace('Room')->group(function () {
        Route::resource('room-types', 'RoomTypeController');
        Route::resource('room', 'RoomController');
    });

    Route::namespace('Guest')->group(function () {
        Route::resource('guest', 'GuestController');
    });

    Route::namespace('Booking')->group(function () {
        Route::get('booking-available-room', 'BookingController@getAvailable')->name('room.available');
        Route::patch('bookings/{booking}/checkin', 'BookingController@checkin')->name('booking.checkin');
        Route::patch('bookings/{booking}/checkout', 'BookingController@checkout')->name('booking.checkout');
        Route::patch('bookings/{booking}/cancel', 'BookingController@cancel')->name('booking.cancel');
        Route::resource('booking', 'BookingController');
        Route::resource('services', 'ServiceController');
    });

    Route::namespace('Master')->group(function() {
        Route::resource('amenity', 'AmenityController');
        Route::resource('source', 'SourceController');
    });
});
