<?php

namespace modules\Hotel\Enums;

enum PaymentStatusEnum: int
{
    case UNPAID = 1;   // Belum bayar
    case PARTIAL = 2;  // Bayar sebagian / DP
    case PAID = 3;     // Lunas
    case REFUNDED = 4; // Dikembalikan

    /**
     * Label untuk tampilan di tabel atau dashboard
     */
    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Belum Bayar',
            self::PARTIAL => 'Dibayar Sebagian',
            self::PAID => 'Lunas',
            self::REFUNDED => 'Dikembalikan',
        };
    }

    /**
     * Warna badge untuk status pembayaran
     */
    public function color(): string
    {
        return match($this) {
            self::UNPAID => 'danger',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
            self::REFUNDED => 'secondary',
        };
    }
}
