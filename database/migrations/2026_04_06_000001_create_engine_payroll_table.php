<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('formula');
            $table->json('config')->nullable();

            $table->date('effective_start');
            $table->date('effective_end')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['code', 'effective_start', 'effective_end']);
        });

        Schema::create('payroll_rule_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_rule_id')->constrained()->cascadeOnDelete();

            $table->double('min')->nullable();
            $table->double('max')->nullable();
            $table->double('rate');

            $table->timestamps();
        });

        Schema::create('payroll_bpjs_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('formula');
            $table->json('config')->nullable();

            $table->date('effective_start');
            $table->date('effective_end')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payroll_bpjs_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_bpjs_rule_id')->constrained('payroll_bpjs_rules')->cascadeOnDelete();

            $table->bigInteger('min')->nullable();
            $table->bigInteger('max')->nullable();
            $table->decimal('rate', 10, 6);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_rules');
        Schema::dropIfExists('payroll_rule_brackets');
    }
};
