<?php

namespace Modules\Hotel\Enums;

enum RoomTypeCategoryEnum: int
{
    case STANDARD = 1;
    case DELUXE = 2;
    case SUITE = 3;
    case PENTHOUSE = 4;

    /**
     * Nama label untuk tampilan di dropdown atau detail kamar
     */
    public function label(): string
    {
        return match($this) {
            self::STANDARD => 'Standard Room',
            self::DELUXE => 'Deluxe Room',
            self::SUITE => 'Suite Room',
            self::PENTHOUSE => 'Penthouse',
        };
    }

    /**
     * Keterangan singkat atau kelas kamar
     */
    public function description(): string
    {
        return match($this) {
            self::STANDARD => 'Kamar standar dengan fasilitas dasar.',
            self::DELUXE => 'Kamar lebih luas dengan pemandangan lebih baik.',
            self::SUITE => 'Kamar mewah dengan ruang tamu terpisah.',
            self::PENTHOUSE => 'Lantai teratas dengan fasilitas eksklusif.',
        };
    }
}
