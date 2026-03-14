<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Output\StreamOutput;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TenantSeeder;

class SolocmsInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solocms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installing Applications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $app = config('app.name');
        $output = new StreamOutput(fopen('php://output', 'w'));

        $this->call('key:generate');

        $this->line("Running {$app} main migration ...");

        // 1. Jalankan migrate:fresh dulu untuk membersihkan database secara total dan sistemik
        // Ini jauh lebih aman daripada DROP TABLE manual satu per satu
        Artisan::call('migrate:fresh', [
            '--database' => 'pgsql',
            '--force'    => true,
        ], $output);

        // 2. Sekarang jalankan migrasi khusus modul CMS
        $this->line("Running {$app} module migrations ...");
        $this->call('migrate', [
            '--path' => 'modules/Cms/database/migrations',
            '--database' => 'pgsql',
            '--force' => true
        ]);

        // 3. Terakhir baru jalankan Seeder (setelah semua tabel sistem & modul terbentuk)
        $this->line("Running {$app} main seeders ...");
        Artisan::call('db:seed', [
            '--database' => 'pgsql',
            '--force'    => true,
        ], $output);

        $this->callSilently('optimize:clear');

        $this->warn("Migrasi selesai");
        $this->info("Terima kasih, sistem sudah terpasang");

        return 0;
    }



    /**
     * Write a new environment file with the given key value.
     *
     * @param  string  $key
     * @param  string  $value
     * @return void
     */
    public function setEnvironmentValue($key, $value)
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('=' . env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
