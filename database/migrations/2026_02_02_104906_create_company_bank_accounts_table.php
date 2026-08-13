<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_profile_id')
                ->constrained('company_profiles')
                ->cascadeOnDelete();

            $table->string('bank_name', 100);
            $table->string('account_number', 50);
            $table->string('account_name', 100);

            $table->enum('purpose', [
                'invoice',
                'tabungan',
                'refund',
                'operational',
            ])->default('invoice');

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['company_profile_id', 'purpose', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_bank_accounts');
    }
};
