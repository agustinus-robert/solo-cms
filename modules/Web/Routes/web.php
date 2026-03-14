<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Modules\Admin\Models\Post;

Route::any('{controller?}/{method?}/{param?}',
    [\Modules\Web\Http\Controllers\MainController::class, 'call']
)->name('web.page');
//pemanggilan

//route('web.page', [
//     'controller' => 'product',
//     'method' => 'detail'
// ]);
