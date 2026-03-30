<?php

namespace Modules\Core\Enums;

enum ScanlogTeacherStatusEnum: int
{
    case DESTROY = 1;
    case RESTORE = 2;
    /**
     * Get the label accessor with label() object
     */
    public function label(): string
    {
        return match ($this) {
            self::DESTROY => 'Pembatalan Jadwal',
            self::RESTORE => 'Pengembalian Jadwal',
        };
    }
}
