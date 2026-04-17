<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\CompanyPtkp;
use Modules\Account\Enums\MariageEnum;

class PtkpDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * * Mengakomodasi:
     * 1. PMK No. 101/PMK.010/2016 (PTKP Rp 54jt)
     * 2. PP No. 58 Tahun 2023 (Kategori TER A, B, C)
     */
    public function run()
    {
        // Truncate jika ingin refresh data (opsional)
        // CompanyPtkp::truncate();

        // Data Dasar dari SQL (24 Status Utama)
        // s: sex (1:L, 2:P), m: marriage, c: child, v: value, cat: category TER
        $rawRecords = [
            // --- LAKI-LAKI (Sex: 1) ---
            ['s' => 1, 'm' => 2, 'c' => 0, 'v' => 54000000, 'cat' => 'A'], // TK/0
            ['s' => 1, 'm' => 2, 'c' => 1, 'v' => 58500000, 'cat' => 'A'], // TK/1
            ['s' => 1, 'm' => 2, 'c' => 2, 'v' => 63000000, 'cat' => 'B'], // TK/2
            ['s' => 1, 'm' => 2, 'c' => 3, 'v' => 67500000, 'cat' => 'B'], // TK/3
            ['s' => 1, 'm' => 1, 'c' => 0, 'v' => 58500000, 'cat' => 'A'], // K/0
            ['s' => 1, 'm' => 1, 'c' => 1, 'v' => 63000000, 'cat' => 'B'], // K/1
            ['s' => 1, 'm' => 1, 'c' => 2, 'v' => 67500000, 'cat' => 'B'], // K/2
            ['s' => 1, 'm' => 1, 'c' => 3, 'v' => 72000000, 'cat' => 'C'], // K/3
            ['s' => 1, 'm' => 4, 'c' => 0, 'v' => 58500000, 'cat' => 'A'], // Duda/Lainnya (Disamakan K/0)
            ['s' => 1, 'm' => 4, 'c' => 1, 'v' => 63000000, 'cat' => 'B'], // Duda/Lainnya (Disamakan K/1)
            ['s' => 1, 'm' => 4, 'c' => 2, 'v' => 67500000, 'cat' => 'B'], // Duda/Lainnya (Disamakan K/2)
            ['s' => 1, 'm' => 4, 'c' => 3, 'v' => 72000000, 'cat' => 'C'], // Duda/Lainnya (Disamakan K/3)

            // --- PEREMPUAN (Sex: 2) ---
            ['s' => 2, 'm' => 2, 'c' => 0, 'v' => 54000000, 'cat' => 'A'], // TK/0
            ['s' => 2, 'm' => 2, 'c' => 1, 'v' => 58500000, 'cat' => 'A'], // TK/1
            ['s' => 2, 'm' => 2, 'c' => 2, 'v' => 63000000, 'cat' => 'B'], // TK/2
            ['s' => 2, 'm' => 2, 'c' => 3, 'v' => 67500000, 'cat' => 'B'], // TK/3
            ['s' => 2, 'm' => 1, 'c' => 0, 'v' => 54000000, 'cat' => 'A'], // K/0 (Istri default TK/0)
            ['s' => 2, 'm' => 1, 'c' => 1, 'v' => 58500000, 'cat' => 'A'], // K/1 (Istri dg tanggungan)
            ['s' => 2, 'm' => 1, 'c' => 2, 'v' => 63000000, 'cat' => 'B'], // K/2 (Istri dg tanggungan)
            ['s' => 2, 'm' => 1, 'c' => 3, 'v' => 67500000, 'cat' => 'B'], // K/3 (Istri dg tanggungan)
            ['s' => 2, 'm' => 3, 'c' => 0, 'v' => 54000000, 'cat' => 'A'], // Cerai/Status 3
            ['s' => 2, 'm' => 3, 'c' => 1, 'v' => 54000000, 'cat' => 'A'],
            ['s' => 2, 'm' => 3, 'c' => 2, 'v' => 54000000, 'cat' => 'A'],
            ['s' => 2, 'm' => 3, 'c' => 3, 'v' => 54000000, 'cat' => 'A'],
        ];

        foreach ($rawRecords as $record) {
            // 1. GENERATE DATA ERA LAMA (2016 - 2023)
            // Rumus: PKP = (Bruto - Biaya Jabatan - Iuran) - PTKP
            CompanyPtkp::create([
                'category'        => null,
                'sex'             => $record['s'],
                'mariage'         => $record['m'],
                'child'           => $record['c'],
                'value'           => $record['v'],
                'effective_start' => '2016-01-01',
                'effective_end'   => '2023-12-31',
            ]);

            // 2. GENERATE DATA ERA BARU (2024 - SEKARANG)
            // Rumus Jan-Nov: Bruto * %TER (berdasarkan Kategori)
            // Rumus Des: Recalculate PKP - PTKP
            CompanyPtkp::create([
                'category'        => $record['cat'], // 'A', 'B', atau 'C'
                'sex'             => $record['s'],
                'mariage'         => $record['m'],
                'child'           => $record['c'],
                'value'           => $record['v'],
                'effective_start' => '2024-01-01',
                'effective_end'   => null, // Masih berlaku
            ]);
        }
    }
}
