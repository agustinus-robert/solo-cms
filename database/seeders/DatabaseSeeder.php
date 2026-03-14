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
            AppDatabaseSeeder::class,
            \Modules\Account\Database\Seeders\AccountDatabaseSeeder::class,
            \Modules\Account\Database\Seeders\UserDatabaseSeeder::class,
            \Modules\Poz\Database\Seeders\ProductDatabaseSeeder::class,
            \Modules\Poz\Database\Seeders\SupplierDatabaseSeeder::class
        ]);
    }
}
