<?php

namespace Modules\HRMS\Enums;

enum TaxTypeEnum: int
{
    case MONTHLY = 1;
    case YEARLY = 2;
    case TER = 3;

    /**
     * Get the label accessor with label() object
     */
    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'PPh 21 perbulan',
            self::YEARLY => 'PPh 21 pertahun',
            self::TER => 'PPh 21 TER'
        };
    }

    /**
     * Get the label accessor with label() object
     */
    public function key(): string
    {
        return match ($this) {
            self::MONTHLY => 'cmp_pph_components',
            self::YEARLY => '',
            self::TER => 'cmp_pph_ter_components'
        };
    }
}
