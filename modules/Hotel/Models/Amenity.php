<?php

namespace modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    protected $table = 'hotel_ref_amenities';
    protected $fillable = ['name', 'icon'];

    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'hotel_room_type_amenities', 'amenity_id', 'room_type_id');
    }
}
