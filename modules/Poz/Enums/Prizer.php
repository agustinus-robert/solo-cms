<?php

namespace Modules\Poz\Enums;

enum Prizer: int
{
    case Product = 1;
    case Bundle = 2;

    public function label(): string
    {
        return match ($this) {
            self::Product => 'Per Produk',
            self::Bundle  => 'Bundle',
        };
    }

    public function countTerms(int $qty_beli, array $config): array
    {
        if ($qty_beli < $config['min_qty']) {
            return ['status' => false, 'message' => 'Minimal beli ' . $config['min_qty']];
        }

        return match ($this) {
            self::Product => $this->logicProduct($qty_beli, $config),
            self::Bundle  => $this->logicBundle($qty_beli, $config),
        };
    }

    private function logicProduct(int $qty, array $config): array
    {
        $hasil = [
            'status' => true,
            'discount' => 0,
            'bonus_item' => null,
            'bonus_qty' => 0
        ];

        if ($config['reward_type'] == 1) {
            $hasil['discount'] = $qty * $config['reward_value'];
        }
        elseif ($config['reward_type'] == 2) {
            $multiplier = floor($qty / $config['min_qty']);
            $hasil['bonus_item'] = $config['bonus_product_id'];
            $hasil['bonus_qty'] = $multiplier * $config['bonus_qty'];
        }

        return $hasil;
    }

    private function logicBundle(int $qty, array $config): array
    {
        return [
            'status' => true,
            'is_bundle_price' => true,
            'special_price' => $config['special_price']
        ];
    }
}
