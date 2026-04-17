<?php

namespace App\Services\Payroll;

class TerRateService
{
    /**
     * Mendapatkan daftar tarif TER berdasarkan kategori PTKP
     */
    public function getRatesByCategory($category)
    {
        return match (strtoupper($category)) {
            'A' => $this->getCategoryA(),
            'B' => $this->getCategoryB(),
            'C' => $this->getCategoryC(),
            default => []
        };
    }

    public function getProgressiveBrackets()
    {
        return [
            ['limit' => 60000000, 'rate' => 0.05],
            ['limit' => 250000000, 'rate' => 0.15],
            ['limit' => 500000000, 'rate' => 0.25],
            ['limit' => 5000000000, 'rate' => 0.30],
            ['limit' => null, 'rate' => 0.35],
        ];
    }

    private function getCategoryA()
    {
        return [
            ['lower' => 0, 'upper' => 5400000, 'percentage' => 0],
            ['lower' => 5400000, 'upper' => 5650000, 'percentage' => 0.25],
            ['lower' => 5650000, 'upper' => 5950000, 'percentage' => 0.5],
            ['lower' => 5950000, 'upper' => 6300000, 'percentage' => 0.75],
            ['lower' => 6300000, 'upper' => 6750000, 'percentage' => 1],
            ['lower' => 6750000, 'upper' => 7500000, 'percentage' => 1.25],
            ['lower' => 7500000, 'upper' => 8550000, 'percentage' => 1.5],
            ['lower' => 8550000, 'upper' => 9650000, 'percentage' => 1.75],
            ['lower' => 9650000, 'upper' => 10650000, 'percentage' => 2],
            ['lower' => 10650000, 'upper' => 12250000, 'percentage' => 2.25],
            ['lower' => 12250000, 'upper' => 14000000, 'percentage' => 2.5],
            ['lower' => 14000000, 'upper' => 16000000, 'percentage' => 3],
            ['lower' => 16000000, 'upper' => 19000000, 'percentage' => 4],
            ['lower' => 19000000, 'upper' => 22000000, 'percentage' => 5],
            ['lower' => 22000000, 'upper' => 25000000, 'percentage' => 6],
            ['lower' => 25000000, 'upper' => 28000000, 'percentage' => 7],
            ['lower' => 28000000, 'upper' => 31000000, 'percentage' => 8],
            ['lower' => 31000000, 'upper' => 34000000, 'percentage' => 9],
            ['lower' => 34000000, 'upper' => 37000000, 'percentage' => 10],
            ['lower' => 37000000, 'upper' => 40000000, 'percentage' => 11],
            ['lower' => 40000000, 'upper' => 43000000, 'percentage' => 12],
            ['lower' => 43000000, 'upper' => 46000000, 'percentage' => 13],
            ['lower' => 46000000, 'upper' => 49000000, 'percentage' => 14],
            ['lower' => 49000000, 'upper' => 52000000, 'percentage' => 15],
            ['lower' => 52000000, 'upper' => 56000000, 'percentage' => 16],
            ['lower' => 56000000, 'upper' => 60000000, 'percentage' => 17],
            ['lower' => 60000000, 'upper' => 64000000, 'percentage' => 18],
            ['lower' => 64000000, 'upper' => 69000000, 'percentage' => 19],
            ['lower' => 69000000, 'upper' => 75000000, 'percentage' => 20],
            ['lower' => 75000000, 'upper' => 81000000, 'percentage' => 21],
            ['lower' => 81000000, 'upper' => 87000000, 'percentage' => 22],
            ['lower' => 87000000, 'upper' => 94000000, 'percentage' => 23],
            ['lower' => 94000000, 'upper' => 102000000, 'percentage' => 24],
            ['lower' => 102000000, 'upper' => 112000000, 'percentage' => 25],
            ['lower' => 112000000, 'upper' => 125000000, 'percentage' => 26],
            ['lower' => 125000000, 'upper' => 141000000, 'percentage' => 27],
            ['lower' => 141000000, 'upper' => 161000000, 'percentage' => 28],
            ['lower' => 161000000, 'upper' => 189000000, 'percentage' => 29],
            ['lower' => 189000000, 'upper' => 218000000, 'percentage' => 30],
            ['lower' => 218000000, 'upper' => 251000000, 'percentage' => 31],
            ['lower' => 251000000, 'upper' => 292000000, 'percentage' => 32],
            ['lower' => 292000000, 'upper' => 345000000, 'percentage' => 33],
            ['lower' => 345000000, 'upper' => null, 'percentage' => 34],
        ];
    }

    private function getCategoryB()
    {
        return [
            ['lower' => 0, 'upper' => 6200000, 'percentage' => 0],
            ['lower' => 6200000, 'upper' => 6500000, 'percentage' => 0.25],
            ['lower' => 6500000, 'upper' => 6850000, 'percentage' => 0.5],
            ['lower' => 6850000, 'upper' => 7300000, 'percentage' => 0.75],
            ['lower' => 7300000, 'upper' => 7800000, 'percentage' => 1],
            ['lower' => 7800000, 'upper' => 8400000, 'percentage' => 1.25],
            ['lower' => 8400000, 'upper' => 9250000, 'percentage' => 1.5],
            ['lower' => 9250000, 'upper' => 10100000, 'percentage' => 1.75],
            ['lower' => 10100000, 'upper' => 11100000, 'percentage' => 2],
            ['lower' => 11100000, 'upper' => 12200000, 'percentage' => 2.25],
            ['lower' => 12200000, 'upper' => 13450000, 'percentage' => 2.5],
            ['lower' => 13450000, 'upper' => 14850000, 'percentage' => 3],
            ['lower' => 14850000, 'upper' => 16350000, 'percentage' => 4],
            ['lower' => 16350000, 'upper' => 18050000, 'percentage' => 5],
            ['lower' => 18050000, 'upper' => 19900000, 'percentage' => 6],
            ['lower' => 19900000, 'upper' => 22000000, 'percentage' => 7],
            ['lower' => 22000000, 'upper' => 24400000, 'percentage' => 8],
            ['lower' => 24400000, 'upper' => 27150000, 'percentage' => 9],
            ['lower' => 27150000, 'upper' => 30250000, 'percentage' => 10],
            ['lower' => 30250000, 'upper' => 33850000, 'percentage' => 11],
            ['lower' => 33850000, 'upper' => 38150000, 'percentage' => 12],
            ['lower' => 38150000, 'upper' => 43400000, 'percentage' => 13],
            ['lower' => 43400000, 'upper' => 49750000, 'percentage' => 14],
            ['lower' => 49750000, 'upper' => 57550000, 'percentage' => 15],
            ['lower' => 57550000, 'upper' => 67350000, 'percentage' => 16],
            ['lower' => 67350000, 'upper' => 79900000, 'percentage' => 17],
            ['lower' => 79900000, 'upper' => 96300000, 'percentage' => 18],
            ['lower' => 96300000, 'upper' => 118450000, 'percentage' => 19],
            ['lower' => 118450000, 'upper' => 148100000, 'percentage' => 20],
            ['lower' => 148100000, 'upper' => 188050000, 'percentage' => 21],
            ['lower' => 188050000, 'upper' => 243250000, 'percentage' => 22],
            ['lower' => 243250000, 'upper' => 323100000, 'percentage' => 23],
            ['lower' => 323100000, 'upper' => 439200000, 'percentage' => 24],
            ['lower' => 439200000, 'upper' => 545800000, 'percentage' => 25],
            ['lower' => 545800000, 'upper' => 635450000, 'percentage' => 26],
            ['lower' => 635450000, 'upper' => 750500000, 'percentage' => 27],
            ['lower' => 750500000, 'upper' => 895300000, 'percentage' => 28],
            ['lower' => 895300000, 'upper' => 1083300000, 'percentage' => 29],
            ['lower' => 1083300000, 'upper' => 1324050000, 'percentage' => 30],
            ['lower' => 1324050000, 'upper' => 1644450000, 'percentage' => 31],
            ['lower' => 1644450000, 'upper' => 2085550000, 'percentage' => 32],
            ['lower' => 2085550000, 'upper' => 2750200000, 'percentage' => 33],
            ['lower' => 2750200000, 'upper' => null, 'percentage' => 34],
        ];
    }

    private function getCategoryC()
    {
        return [
            ['lower' => 0, 'upper' => 6600000, 'percentage' => 0],
            ['lower' => 6600000, 'upper' => 6950000, 'percentage' => 0.25],
            ['lower' => 6950000, 'upper' => 7350000, 'percentage' => 0.5],
            ['lower' => 7350000, 'upper' => 7800000, 'percentage' => 0.75],
            ['lower' => 7800000, 'upper' => 8300000, 'percentage' => 1],
            ['lower' => 8300000, 'upper' => 8850000, 'percentage' => 1.25],
            ['lower' => 8850000, 'upper' => 9650000, 'percentage' => 1.5],
            ['lower' => 9650000, 'upper' => 10450000, 'percentage' => 1.75],
            ['lower' => 10450000, 'upper' => 11350000, 'percentage' => 2],
            ['lower' => 11350000, 'upper' => 12350000, 'percentage' => 2.25],
            ['lower' => 12350000, 'upper' => 13500000, 'percentage' => 2.5],
            ['lower' => 13500000, 'upper' => 14750000, 'percentage' => 3],
            ['lower' => 14750000, 'upper' => 16150000, 'percentage' => 4],
            ['lower' => 16150000, 'upper' => 17750000, 'percentage' => 5],
            ['lower' => 17750000, 'upper' => 19500000, 'percentage' => 6],
            ['lower' => 19500000, 'upper' => 21450000, 'percentage' => 7],
            ['lower' => 21450000, 'upper' => 23600000, 'percentage' => 8],
            ['lower' => 23600000, 'upper' => 26100000, 'percentage' => 9],
            ['lower' => 26100000, 'upper' => 28900000, 'percentage' => 10],
            ['lower' => 28900000, 'upper' => 32100000, 'percentage' => 11],
            ['lower' => 32100000, 'upper' => 35850000, 'percentage' => 12],
            ['lower' => 35850000, 'upper' => 40300000, 'percentage' => 13],
            ['lower' => 40300000, 'upper' => 45700000, 'percentage' => 14],
            ['lower' => 45700000, 'upper' => 52350000, 'percentage' => 15],
            ['lower' => 52350000, 'upper' => 60750000, 'percentage' => 16],
            ['lower' => 60750000, 'upper' => 71450000, 'percentage' => 17],
            ['lower' => 71450000, 'upper' => 85250000, 'percentage' => 18],
            ['lower' => 85250000, 'upper' => 103550000, 'percentage' => 19],
            ['lower' => 103550000, 'upper' => 128750000, 'percentage' => 20],
            ['lower' => 128750000, 'upper' => 164400000, 'percentage' => 21],
            ['lower' => 164400000, 'upper' => 215350000, 'percentage' => 22],
            ['lower' => 215350000, 'upper' => 290000000, 'percentage' => 23],
            ['lower' => 290000000, 'upper' => 401400000, 'percentage' => 24],
            ['lower' => 401400000, 'upper' => 512250000, 'percentage' => 25],
            ['lower' => 512250000, 'upper' => 609200000, 'percentage' => 26],
            ['lower' => 609200000, 'upper' => 728850000, 'percentage' => 27],
            ['lower' => 728850000, 'upper' => 880650000, 'percentage' => 28],
            ['lower' => 880650000, 'upper' => 1077750000, 'percentage' => 29],
            ['lower' => 1077750000, 'upper' => 1334000000, 'percentage' => 30],
            ['lower' => 1334000000, 'upper' => 1678850000, 'percentage' => 31],
            ['lower' => 1678850000, 'upper' => 2162900000, 'percentage' => 32],
            ['lower' => 2162900000, 'upper' => 2902300000, 'percentage' => 33],
            ['lower' => 2902300000, 'upper' => null, 'percentage' => 34],
        ];
    }
}
