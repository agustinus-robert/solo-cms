<?php

namespace Modules\Hotel\Enums;

enum InventoryTypeEnum: int
{
    case ASSET = 1;
    case CONSUMABLE = 2;

    public function label(): string
    {
        return match($this) {
            self::ASSET => 'Asset (Tetap)',
            self::CONSUMABLE => 'Consumable (Habis Pakai)',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::ASSET => 'bg-soft-primary text-primary',
            self::CONSUMABLE => 'bg-soft-info text-info',
        };
    }
}
