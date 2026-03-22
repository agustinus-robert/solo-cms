<?php

namespace Modules\Web\Models;

use Illuminate\Database\Eloquent\Model;

class Whistlist extends Model
{
    protected $table = 'product_whistlists';

    protected $fillable = [
        'session_id',
        'user_id',
        'items',
    ];

    protected $casts = [
        'items' => 'array'
    ];
}
