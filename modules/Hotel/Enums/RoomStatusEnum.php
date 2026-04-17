<?php

namespace modules\Hotel\Enums;

enum RoomStatusEnum: int
{
    case AVAILABLE = 1;     // Siap huni
    case OCCUPIED = 2;      // Sedang diisi tamu
    case DIRTY = 3;         // Perlu dibersihkan
    case CLEANING = 4;      // Sedang dibersihkan
    case MAINTENANCE = 5;   // Dalam perbaikan (OOO)

    /**
     * Label untuk tampilan di UI
     */
    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Siap Huni',
            self::OCCUPIED => 'Terisi',
            self::DIRTY => 'Kotor',
            self::CLEANING => 'Pembersihan',
            self::MAINTENANCE => 'Perbaikan',
        };
    }

    /**
     * Warna CSS/Bootstrap untuk Badge
     */
    public function color(): string
    {
        return match($this) {
            self::AVAILABLE => 'success',
            self::OCCUPIED => 'danger',
            self::DIRTY => 'warning',
            self::CLEANING => 'info',
            self::MAINTENANCE => 'secondary',
        };
    }
}
