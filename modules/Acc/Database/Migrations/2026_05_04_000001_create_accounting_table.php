<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acc_coas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('category', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->timestamps();
        });

        Schema::create('acc_ledgers', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('reference_number')->index();
            $table->string('description')->nullable();
            $table->string('source_module');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });

        Schema::create('acc_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_id')->constrained('acc_ledgers')->onDelete('cascade');
            $table->foreignId('coa_id')->constrained('acc_coas');
            $table->string('department_tag')->nullable();

            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('acc_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        Schema::create('acc_beginning_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('acc_periods');
            $table->foreignId('coa_id')->constrained('acc_coas');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('acc_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('transaction_type');
            $table->foreignId('coa_id')->constrained('acc_coas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
