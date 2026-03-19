<?php

namespace Modules\Web\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'product_wishlists';

    protected $fillable = [
        'session_id',
        'user_id',
        'items',
    ];

    protected $casts = [
        'items' => 'json'
    ];
}
