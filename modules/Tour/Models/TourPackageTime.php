<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPackageTime extends Model
{
    use SoftDeletes;
    /**
     * Nama tabel jika tidak mengikuti konvensi plural Laravel (optional).
     */
    protected $table = 'tour_package_times';

    /**
     * Atribut yang dapat diisi melalui mass assignment.
     */
    protected $fillable = [
        'tour_package_id',
        'tour_location_id',
        'departure_time',
        'meeting_point',
    ];

    /**
     * Relasi balik ke Package.
     * Setiap jam keberangkatan dimiliki oleh satu paket tour.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    /**
     * Relasi ke Master Lokasi/Daerah.
     * Mengambil data daerah penjemputan untuk jam ini.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(TourLocation::class, 'tour_location_id');
    }

    /**
     * Accessor untuk format jam yang lebih cantik (optional).
     * Contoh: $time->formatted_time akan menghasilkan "08:00"
     */
    public function getFormattedTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->departure_time)->format('H:i');
    }
}
