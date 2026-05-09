<?php

namespace Modules\Acc\Enums;

enum LedgerType: string
{
    case GENERAL = 'general';
    case ADJUSTMENT = 'adjustment';
    case CLOSING = 'closing';

    public function label(): string
    {
        return match($this) {
            self::GENERAL => 'Jurnal Umum',
            self::ADJUSTMENT => 'Jurnal Penyesuaian',
            self::CLOSING => 'Jurnal Penutup',
        };
    }
}
