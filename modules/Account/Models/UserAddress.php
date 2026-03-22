<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserAddress extends Model
{
    protected $table = 'user_address';


    protected $fillable = [
        'user_id', 'label', 'receiver_name', 'phone', 'address',
        'rt', 'rw', 'village', 'district_id', 'postal', 'is_main'
    ];

    protected $casts = [
        'postal' => 'integer',
        'is_main' => 'boolean'
    ];

    public function getFullAttribute() {
        return join(', ', array_filter([
            $this->address,
            $this->rt ? 'RT '.$this->rt : null,
            $this->rw ? 'RW '.$this->rw : null,
            $this->village,
            $this->regional,
            $this->postal
        ]));
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function district() {
        return $this->belongsTo('\App\Models\References\ProvinceRegencyDistrict', 'district_id')->withDefault();
    }
}
