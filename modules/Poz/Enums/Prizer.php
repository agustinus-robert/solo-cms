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

    /**
     * @param int $qty_beli Total quantity item yang sesuai kriteria
     * @param array $config Konfigurasi dari database
     * @param int|null $current_product_id ID produk yang sedang dicek
     * @param int|null $current_category_id ID kategori produk yang sedang dicek
     */
    public function countTerms(int $qty_beli, array $config, $current_product_id = null, $current_category_id = null): array
    {
        // 1. Validasi apakah produk ini masuk dalam kriteria promo
        if (!$this->isProductEligible($config, $current_product_id, $current_category_id)) {
            return ['status' => false, 'message' => 'Produk tidak termasuk dalam promo ini'];
        }

        // 2. Validasi Minimal Qty
        if ($qty_beli < ($config['min_qty'] ?? 1)) {
            return ['status' => false, 'message' => 'Minimal beli ' . ($config['min_qty'] ?? 1)];
        }

        return match ($this) {
            self::Product => $this->logicProduct($qty_beli, $config),
            self::Bundle  => $this->logicBundle($qty_beli, $config),
        };
    }

    /**
     * Mengecek apakah produk/kategori sesuai dengan config promo
     */
    private function isProductEligible(array $config, $productId, $categoryId): bool
    {
        if ($this === self::Product) {
            return $productId == ($config['product_id'] ?? null);
        }

        if ($this === self::Bundle) {
            $allowedProducts = $config['bundle_product_ids'] ?? [];
            $allowedCategories = $config['bundle_category_ids'] ?? [];

            // Jika produk terdaftar di list produk bundle OR kategorinya terdaftar di list kategori bundle
            return in_array($productId, $allowedProducts) || in_array($categoryId, $allowedCategories);
        }

        return false;
    }

    private function logicProduct(int $qty, array $config): array
    {
        $hasil = [
            'status' => true,
            'discount' => 0,
            'bonus_item' => null,
            'bonus_qty' => 0
        ];

        if (($config['reward_type'] ?? 1) == 1) {
            $hasil['discount'] = $qty * ($config['reward_value'] ?? 0);
        }
        elseif (($config['reward_type'] ?? 1) == 2) {
            $min = $config['min_qty'] ?: 1;
            $multiplier = floor($qty / $min);
            $hasil['bonus_item'] = $config['bonus_product_id'] ?? null;
            $hasil['bonus_qty'] = $multiplier * ($config['bonus_qty'] ?? 0);
        }

        return $hasil;
    }

    private function logicBundle(int $qty, array $config): array
    {
        return [
            'status' => true,
            'is_bundle_price' => true,
            'special_price' => $config['special_price'] ?? 0
        ];
    }
}
