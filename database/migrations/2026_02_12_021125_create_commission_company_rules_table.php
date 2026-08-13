<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commission_company_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commission_scheme_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('paket_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('amount_per_closing', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_company_rules');
    }
};
