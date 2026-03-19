<?php

namespace Modules\Poz\Models;

use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    protected $table = 'product_carts';

    protected $fillable = [
        'session_id',
        'user_id',
        'items',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'items' => 'json'
    ];
}
