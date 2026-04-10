<?php

namespace Modules\Poz\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sqlFiles = [
            'outlet.sql',
            'brand.sql', 'outlet_brand.sql',
            'category.sql', 'outlet_category.sql',
            'unit.sql', 'outlet_unit.sql',
            'tax_rate.sql', 'outlet_tax_rate.sql',
            'product.sql', 'outlet_product.sql',
            'user_employee_outlet.sql', 'user_tokens.sql'
        ];

        // Matikan Foreign Key Check (PostgreSQL)
        DB::statement("SET session_replication_role = 'replica';");

        foreach ($sqlFiles as $file) {
            $path = base_path("modules/Poz/Database/Seeders/sql/{$file}");
            if (File::exists($path)) {
                $this->command->info("Importing: {$file}");
                $sql = File::get($path);
                DB::unprepared($sql);
            } else {
                $this->command->error("File tidak ditemukan: {$file}");
            }
        }

        // Hidupkan kembali Foreign Key Check
        DB::statement("SET session_replication_role = 'origin';");

        // RESET SEQUENCE POSTGRESQL (Agar tidak error Unique Violation)
        $this->syncSequences();

        $this->command->info("Import SQL dan sinkronisasi sequence selesai.");
    }

    /**
     * Otomatis sinkronisasi semua sequence PostgreSQL berdasarkan tabel yang ada
     */
    private function syncSequences()
    {
        if (config('database.default') !== 'pgsql') return;

        $this->command->info("Menyelaraskan sequence PostgreSQL...");

        // Ambil semua nama tabel di schema public yang punya kolom ID
        $tables = DB::select("
            SELECT table_name
            FROM information_schema.columns
            WHERE table_schema = 'public'
            AND column_name = 'id'
            AND column_default LIKE 'nextval%'
        ");

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            // Query untuk reset sequence ke ID tertinggi saat ini
            DB::statement("
                SELECT setval(
                    pg_get_serial_sequence('{$tableName}', 'id'),
                    coalesce(max(id), 1)
                ) FROM {$tableName}
            ");
        }
    }
}
