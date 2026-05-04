<?php

namespace Modules\Acc\Enums;

enum AccountCategory: string
{
    case ASSET = 'asset';
    case LIABILITY = 'liability';
    case EQUITY = 'equity';
    case REVENUE = 'revenue';
    case EXPENSE = 'expense';

    public function label(): string
    {
        return match($this) {
            self::ASSET => 'Aset',
            self::LIABILITY => 'Kewajiban / Hutang',
            self::EQUITY => 'Ekuitas / Modal',
            self::REVENUE => 'Pendapatan',
            self::EXPENSE => 'Beban / Biaya',
        };
    }
}
