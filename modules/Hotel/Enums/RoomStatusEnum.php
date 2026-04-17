<?php

namespace modules\Hotel\Enums;

enum RoomStatusEnum: string
{
    case AVAILABLE = 1;     // Siap huni
    case OCCUPIED = 2;      // Sedang diisi tamu
    case DIRTY = 3;         // Perlu dibersihkan
    case CLEANING = 4;      // Sedang dibersihkan
    case MAINTENANCE = 5;   // Dalam perbaikan (OOO)
}
