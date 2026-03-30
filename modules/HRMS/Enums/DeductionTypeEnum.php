<?php

namespace Modules\HRMS\Enums;

enum DeductionTypeEnum: int
{
    case INSUFICIENT = 1;
    case OVER = 2;

    /**
     * Get the label accessor with label() object
     */
    public function label(): string
    {
        return match ($this) {
            self::INSUFICIENT => 'Kurang bayar',
            self::OVER => 'Kelebihan bayar'
        };
    }
}
