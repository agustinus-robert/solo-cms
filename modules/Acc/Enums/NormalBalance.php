<?php

namespace Modules\Acc\Enums;

enum NormalBalance: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match($this) {
            self::DEBIT => 'Debit',
            self::CREDIT => 'Kredit',
        };
    }
}
