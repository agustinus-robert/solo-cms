<?php

namespace Modules\HRMS\Enums;

enum TransferTypeEnum: int
{
    case JOGJA = 1;
    case JAKARTA = 2;

    /**
     * Get the label accessor with label() object
     */
    public function label(): string
    {
        return match ($this) {
            self::JOGJA => 'Yogyakarta',
            self::JAKARTA => 'Jakarta'
        };
    }
}
