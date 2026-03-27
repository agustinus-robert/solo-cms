<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// if (env('BUG') == 0) {
    Route::prefix('builder')->namespace('Builder')->name('builder.')->group(function () {
        Route::get('/datatable', 'DataTableBuilderController@index')->name('datatable');
        Route::get('customs/datatable', 'CustomsController@getTable')->name('customs.datatables');
    });

    Route::prefix('configure')->namespace('Configure')->name('configure.')->group(function () {
        Route::get('/datatable', 'DataTableConfigureController@index')->name('datatable');
    });
//}

Route::middleware('auth')->group(function () {
    Route::get('/dashboard-cms', 'DashboardCmsController@index')->name('dashboard');
    Route::get('/live-editor-access', 'LiveEditorAccessController@create')->name('live-editor-access');
    Route::post('/live-editor-access', 'LiveEditorAccessController@store')->name('live-editor-access.store');

    Route::prefix('builder')->namespace('Builder')->name('builder.')->group(function () {
        Route::resourcePermission('posting', 'PostingController', 'posting')->parameters(['postings' => 'posting']);
        Route::resourcePermission('custom', 'CustomsController', 'custom')->parameters(['customs' => 'custom']);
        Route::resourcePermission('posting_image', 'PostingImageController', 'postImage')->parameters(['postings_images' => 'posting_images']);
        Route::resourcePermission('posting_video', 'PostingVideoController', 'postVideo')->parameters(['postings_video' => 'posting_video']);
        Route::resourcePermission('menu', 'MenuController', 'menu')->parameters(['menus' => 'menu']);
        Route::resourcePermission('order', 'OrderController', 'order')->parameters(['orders' => 'order']);
        Route::resourcePermission('category', 'CategoryzationController', 'category')->parameters(['categorys' => 'category']);

        Route::get('/posting/{posting}/publish', 'PostingController@publish')->name('publish');
        Route::get('/posting/{posting}/draft', 'PostingController@draft')->name('draft');

        Route::get('/posting/{posting}/schedule_view', 'PostingController@sch_date')->name('view_schedule');
        Route::post('/posting/{posting}/schedule_post', 'PostingController@post_sch')->name('post_schedule');
        Route::post('/posting/{posting}/schedule_cancel', 'PostingController@cancel_post_sch')->name('cancel_schedule');

        //  Route::resource('role', 'RoleController')->parameters(['roles' => 'role']);
    });

    Route::prefix('configure')->namespace('Configure')->name('configure.')->group(function () {
        Route::resourcePermission('categoryzation_name', 'CategoryzationNameController', 'categoryName')->parameters(['categoryzation_names' => 'categoryzation_name']);
        Route::resourcePermission('tags', 'TagsController', 'tags')->parameters(['tags' => 'tag']);
    });
});
