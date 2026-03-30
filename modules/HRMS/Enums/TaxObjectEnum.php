<?php

namespace Modules\HRMS\Enums;

enum TaxObjectEnum: int
{
    case COMPANY = 1;
    case EMPLOYEE = 2;

    /**
     * Get the label accessor with label() object
     */
    public function label(): string
    {
        return match ($this) {
            self::COMPANY => 'Perusahaan',
            self::EMPLOYEE => 'Karyawan',
        };
    }

    /**
     * Get the label accessor with label() object
     */
    public function key(): string
    {
        return match ($this) {
            self::COMPANY => 'company',
            self::EMPLOYEE => 'employee',
        };
    }
}
