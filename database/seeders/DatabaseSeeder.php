<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Account\Models\User;
use Modules\HRMS\Models\Employee;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TaxRatesSeeder::class,
            \Modules\Core\Database\Seeders\MomentDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\DepartementDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\ContractDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\VacationDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\LeaveDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\InsuranceDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\OutworkDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\SlipDatabaseSeeder::class,
            \Modules\Core\Database\Seeders\LoanDatabaseSeeder::class,
            OnlyDataSeeder::class,
            PermitSeeder::class
        ]);
    }
}
