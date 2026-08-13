<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->string('nama', 100)->nullable();
            $table->string('kode_agent', 30)->unique();
            $table->string('phone', 20)->nullable();

            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();

            $table->decimal('komisi_persen', 5, 2)->default(0);
            $table->decimal('komisi_manual', 5, 2)->default(0);
            $table->decimal('komisi_affiliate', 5, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('user_id'); // unique_user_agent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
