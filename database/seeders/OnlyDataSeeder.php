<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OnlyDataSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/sql/test3.sql');

        if (!File::exists($path)) {
            $this->command->error("File tidak ditemukan: $path");
            return;
        }

        $this->command->info("Memulai Import (Skip data yang sudah ada)...");

        // Matikan proteksi foreign key
        DB::statement("SET session_replication_role = 'replica'");

        $handle = fopen($path, "r");
        $fullStatement = "";
        $isInsideComment = false;

        DB::beginTransaction();

        try {
            while (($line = fgets($handle)) !== false) {
                $trim = trim($line);

                // 1. Abaikan baris kosong
                if ($trim === '') continue;

                // 2. Deteksi dan lewati komentar blok /* ... */ (Penyebab error Navicat tadi)
                if (str_starts_with($trim, '/*')) {
                    $isInsideComment = true;
                }
                if ($isInsideComment) {
                    if (str_contains($trim, '*/')) {
                        $isInsideComment = false;
                    }
                    continue;
                }

                // 3. Abaikan komentar satu baris, BEGIN, dan COMMIT
                if (str_starts_with($trim, '--') || $trim === 'BEGIN;' || $trim === 'COMMIT;') {
                    continue;
                }

                $fullStatement .= $line;

                // 4. Jika menemukan akhir perintah (;)
                if (str_ends_with($trim, ';')) {
                    if (stripos($fullStatement, 'INSERT INTO') !== false) {

                        // Tambahkan 'ON CONFLICT DO NOTHING' agar kalau ID sudah ada, dia LEWATI (Skip)
                        $rawSql = rtrim(trim($fullStatement), ';');
                        $safeSql = $rawSql . " ON CONFLICT DO NOTHING;";

                        DB::unprepared($safeSql);
                    }
                    $fullStatement = "";
                }
            }

            DB::commit();
            $this->command->info("Import selesai dengan sukses.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Gagal Import: " . $e->getMessage());
        } finally {
            if (is_resource($handle)) fclose($handle);
            DB::statement("SET session_replication_role = 'origin'");
        }

        $this->resetSequences();
    }

    protected function resetSequences()
    {
        $tables = DB::select("SELECT table_name FROM information_schema.columns WHERE table_schema = 'public' AND column_name = 'id'");
        foreach ($tables as $table) {
            $name = $table->table_name;
            try {
                $sequence = DB::selectOne("SELECT pg_get_serial_sequence('public.\"$name\"','id') AS seq");
                if ($sequence && $sequence->seq) {
                    DB::statement("SELECT setval('{$sequence->seq}', COALESCE((SELECT MAX(id) FROM public.\"$name\"), 1))");
                }
            } catch (\Exception $e) { continue; }
        }
    }
}
