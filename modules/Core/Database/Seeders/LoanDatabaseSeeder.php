<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\CompanyLoanCategory;

class LoanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $loans = [
            [
                'name' => 'Bunga Pinjaman',
                'type' => 2,
            ],
            [
                'name'        => 'Cicilan Pinjaman',
                'interest_id' => 1,
                'type'        => 1,
            ],
            [
                'name' => 'Cash Bon',
                'type' => 1,
            ],
        ];

        foreach ($loans as $loan) {
            CompanyLoanCategory::create($loan);
        }
    }
}
