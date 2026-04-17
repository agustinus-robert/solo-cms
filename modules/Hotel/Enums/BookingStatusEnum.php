<?php

namespace modules\Hotel\Enums;

enum BookingStatusEnum: int
{
    case PENDING = 1;     // Menunggu pembayaran/konfirmasi
    case CONFIRMED = 2;   // Reservasi sudah sah
    case CHECKED_IN = 3;  // Tamu sudah di lokasi
    case CHECKED_OUT = 4; // Tamu sudah selesai
    case CANCELLED = 5;   // Pesanan dibatalkan

    /**
     * Label Bahasa Indonesia untuk tampilan UI
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu',
            self::CONFIRMED => 'Terkonfirmasi',
            self::CHECKED_IN => 'Sudah Check-in',
            self::CHECKED_OUT => 'Sudah Check-out',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    /**
     * Warna Badge untuk status booking
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'primary',
            self::CHECKED_IN => 'success',
            self::CHECKED_OUT => 'info',
            self::CANCELLED => 'danger',
        };
    }
}
