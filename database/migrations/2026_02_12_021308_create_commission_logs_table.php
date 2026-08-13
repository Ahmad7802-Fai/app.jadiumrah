<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commission_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commission_scheme_id')->constrained()->cascadeOnDelete();

            // 🔥 TAMBAH INI
            $table->foreignId('jamaah_id')
                ->nullable()
                ->constrained('jamaahs')
                ->cascadeOnDelete();

            $table->decimal('company_amount', 15, 2);
            $table->decimal('branch_amount', 15, 2);
            $table->decimal('agent_amount', 15, 2);

            $table->timestamps();

            // 🔥 optional unique
            $table->unique(['booking_id','jamaah_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('commission_logs');
    }
};
