<?php

namespace Modules\Poz\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMeta extends Model
{
    protected $table = 'product_metas';
    protected $fillable = ['product_id', 'meta_key', 'meta_value'];
    public $timestamps = false;
}
