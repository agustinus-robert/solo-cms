<?php

namespace modules\Hotel\Enums;

enum BookingStatus: string
{
    case PENDING = 1;     // Menunggu pembayaran/konfirmasi
    case CONFIRMED = 2;    // Reservasi sudah sah
    case CHECKED_IN = 3;       // Tamu sudah di lokasi
    case CHECKED_OUT = 4;     // Tamu sudah selesai
    case CANCELLED = 5; // Pesanan dibatalkan
}
