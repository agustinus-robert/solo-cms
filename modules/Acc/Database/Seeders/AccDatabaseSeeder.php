<?php

namespace Modules\Acc\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acc\Models\Coa;
use Modules\Acc\Models\Period;
use Modules\Acc\Models\AccMapping;
use Modules\Acc\Models\BeginningBalance;
use Modules\Acc\Models\Ledger;
use Modules\Acc\Models\LedgerEntry;
use Modules\Acc\Enums\AccountCategory;
use Modules\Acc\Enums\NormalBalance;
use Modules\Acc\Enums\LedgerType;
use Illuminate\Support\Facades\DB;

class AccDatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // 1. SEED PERIODE
            $period = Period::updateOrCreate(
                ['name' => 'Mei 2026'],
                [
                    'start_date' => '2026-05-01',
                    'end_date' => '2026-05-31',
                    'is_closed' => false
                ]
            );

            // 2. SEED COA
            $this->seedCoa();

            // 3. SEED BEGINNING BALANCE
            $this->seedBalances($period);

            // 4. SEED MAPPING
            $this->seedMapping();

            // 5. SEED DUMMY TRANSACTIONS (Ledger & LedgerEntry)
            $this->seedDummyTransactions();
        });
    }

    private function seedCoa()
    {
        $coaData = [
            // ASSETS
            ['code' => '1101', 'name' => 'Kas Utama', 'category' => AccountCategory::ASSET, 'normal_balance' => NormalBalance::DEBIT],
            ['code' => '1102', 'name' => 'Bank Utama', 'category' => AccountCategory::ASSET, 'normal_balance' => NormalBalance::DEBIT],
            ['code' => '1201', 'name' => 'Piutang Tamu', 'category' => AccountCategory::ASSET, 'normal_balance' => NormalBalance::DEBIT],
            ['code' => '1301', 'name' => 'Persediaan Barang POS', 'category' => AccountCategory::ASSET, 'normal_balance' => NormalBalance::DEBIT],

            // LIABILITIES
            ['code' => '2101', 'name' => 'Hutang Dagang', 'category' => AccountCategory::LIABILITY, 'normal_balance' => NormalBalance::CREDIT],
            ['code' => '2201', 'name' => 'Titipan Deposit Tamu', 'category' => AccountCategory::LIABILITY, 'normal_balance' => NormalBalance::CREDIT],

            // EQUITY
            ['code' => '3101', 'name' => 'Modal Pemilik', 'category' => AccountCategory::EQUITY, 'normal_balance' => NormalBalance::CREDIT],
            ['code' => '3201', 'name' => 'Laba Ditahan', 'category' => AccountCategory::EQUITY, 'normal_balance' => NormalBalance::CREDIT],

            // REVENUE
            ['code' => '4101', 'name' => 'Pendapatan Kamar Hotel', 'category' => AccountCategory::REVENUE, 'normal_balance' => NormalBalance::CREDIT],
            ['code' => '4201', 'name' => 'Pendapatan Paket Tour', 'category' => AccountCategory::REVENUE, 'normal_balance' => NormalBalance::CREDIT],
            ['code' => '4301', 'name' => 'Pendapatan Penjualan POS', 'category' => AccountCategory::REVENUE, 'normal_balance' => NormalBalance::CREDIT],

            // EXPENSES
            ['code' => '5101', 'name' => 'HPP POS', 'category' => AccountCategory::EXPENSE, 'normal_balance' => NormalBalance::DEBIT],
            ['code' => '5201', 'name' => 'Beban Gaji Karyawan', 'category' => AccountCategory::EXPENSE, 'normal_balance' => NormalBalance::DEBIT],
            ['code' => '5202', 'name' => 'Beban Listrik, Air & WiFi', 'category' => AccountCategory::EXPENSE, 'normal_balance' => NormalBalance::DEBIT],
            ['code' => '5301', 'name' => 'Beban Komisi Vendor / Guide', 'category' => AccountCategory::EXPENSE, 'normal_balance' => NormalBalance::DEBIT],
        ];

        foreach ($coaData as $item) {
            Coa::updateOrCreate(['code' => $item['code']], $item);
        }
    }

    private function seedBalances($period)
    {
        $kas = Coa::where('code', '1101')->first();
        $modal = Coa::where('code', '3101')->first();

        if ($kas && $modal) {
            BeginningBalance::updateOrCreate(
                ['period_id' => $period->id, 'coa_id' => $kas->id],
                ['amount' => 100000000]
            );

            BeginningBalance::updateOrCreate(
                ['period_id' => $period->id, 'coa_id' => $modal->id],
                ['amount' => 100000000]
            );
        }
    }

    private function seedMapping()
    {
        $kas = Coa::where('code', '1101')->first()?->id;
        $pendHotel = Coa::where('code', '4101')->first()?->id;
        $pendTour = Coa::where('code', '4201')->first()?->id;
        $pendPOS = Coa::where('code', '4301')->first()?->id;
        $hpp = Coa::where('code', '5101')->first()?->id;
        $stok = Coa::where('code', '1301')->first()?->id;

        if ($kas) {
            $mappings = [
                ['module' => 'hotel', 'transaction_type' => 'payment_debit', 'coa_id' => $kas],
                ['module' => 'hotel', 'transaction_type' => 'payment_credit', 'coa_id' => $pendHotel],
                ['module' => 'tour', 'transaction_type' => 'payment_debit', 'coa_id' => $kas],
                ['module' => 'tour', 'transaction_type' => 'payment_credit', 'coa_id' => $pendTour],
                ['module' => 'pos', 'transaction_type' => 'sale_debit', 'coa_id' => $kas],
                ['module' => 'pos', 'transaction_type' => 'sale_credit', 'coa_id' => $pendPOS],
                ['module' => 'pos', 'transaction_type' => 'cogs_debit', 'coa_id' => $hpp],
                ['module' => 'pos', 'transaction_type' => 'cogs_credit', 'coa_id' => $stok],
            ];

            foreach ($mappings as $map) {
                if ($map['coa_id']) {
                    AccMapping::updateOrCreate([
                        'module' => $map['module'],
                        'transaction_type' => $map['transaction_type']
                    ], $map);
                }
            }
        }
    }

    private function seedDummyTransactions()
    {
        $kas = Coa::where('code', '1101')->first()?->id;
        $pendHotel = Coa::where('code', '4101')->first()?->id;
        $pendTour = Coa::where('code', '4201')->first()?->id;
        $pendPOS = Coa::where('code', '4301')->first()?->id;
        $hpp = Coa::where('code', '5101')->first()?->id;
        $stok = Coa::where('code', '1301')->first()?->id;

        if (!$kas) return;

        // 1. TRANSAKSI HOTEL
        $l1 = Ledger::create([
            'transaction_date' => '2026-05-04',
            'reference_number' => 'INV-HOT-001',
            'description' => 'Booking Room 101 - Robert',
            'source_module' => 'hotel',
            'user_id' => 2,
            'type' => LedgerType::GENERAL // Tambahkan LedgerType
        ]);
        LedgerEntry::create(['ledger_id' => $l1->id, 'coa_id' => $kas, 'department_tag' => 'hotel', 'debit' => 750000, 'credit' => 0]);
        LedgerEntry::create(['ledger_id' => $l1->id, 'coa_id' => $pendHotel, 'department_tag' => 'hotel', 'debit' => 0, 'credit' => 750000]);

        // 2. TRANSAKSI TOUR
        $l2 = Ledger::create([
            'transaction_date' => '2026-05-04',
            'reference_number' => 'INV-TOUR-001',
            'description' => 'Paket Tour Borobudur',
            'source_module' => 'tour',
            'user_id' => 2,
            'type' => LedgerType::GENERAL // Tambahkan LedgerType
        ]);
        LedgerEntry::create(['ledger_id' => $l2->id, 'coa_id' => $kas, 'department_tag' => 'tour', 'debit' => 1200000, 'credit' => 0]);
        LedgerEntry::create(['ledger_id' => $l2->id, 'coa_id' => $pendTour, 'department_tag' => 'tour', 'debit' => 0, 'credit' => 1200000]);

        // 3. TRANSAKSI POS
        $l3 = Ledger::create([
            'transaction_date' => '2026-05-04',
            'reference_number' => 'INV-POS-001',
            'description' => 'Penjualan Cafe',
            'source_module' => 'pos',
            'user_id' => 2,
            'type' => LedgerType::GENERAL // Tambahkan LedgerType
        ]);
        LedgerEntry::create(['ledger_id' => $l3->id, 'coa_id' => $kas, 'department_tag' => 'pos', 'debit' => 150000, 'credit' => 0]);
        LedgerEntry::create(['ledger_id' => $l3->id, 'coa_id' => $pendPOS, 'department_tag' => 'pos', 'debit' => 0, 'credit' => 150000]);
        LedgerEntry::create(['ledger_id' => $l3->id, 'coa_id' => $hpp, 'department_tag' => 'pos', 'debit' => 60000, 'credit' => 0]);
        LedgerEntry::create(['ledger_id' => $l3->id, 'coa_id' => $stok, 'department_tag' => 'pos', 'debit' => 0, 'credit' => 60000]);

        // Contoh Seeding untuk Jurnal Penyesuaian (Adjustment)
        $l4 = Ledger::create([
            'transaction_date' => '2026-05-31',
            'reference_number' => 'ADJ-001',
            'description' => 'Penyesuaian Beban Listrik Mei',
            'source_module' => 'manual',
            'user_id' => 2,
            'type' => LedgerType::ADJUSTMENT // Menggunakan tipe ADJUSTMENT
        ]);
        $listrik = Coa::where('code', '5202')->first()?->id;
        LedgerEntry::create(['ledger_id' => $l4->id, 'coa_id' => $listrik, 'debit' => 4600000, 'credit' => 0]);
        LedgerEntry::create(['ledger_id' => $l4->id, 'coa_id' => $kas, 'debit' => 0, 'credit' => 4600000]);
    }
}
