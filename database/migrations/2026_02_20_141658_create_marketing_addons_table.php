<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('booking_addons', function (Blueprint $table) {
        $table->id();

        $table->foreignId('booking_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('marketing_addon_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->integer('qty')->default(1);

        $table->decimal('price', 15, 2); // snapshot harga saat booking
        $table->decimal('cost_price', 15, 2)->default(0);

        $table->decimal('total', 15, 2);

        $table->timestamps();

        $table->unique(['booking_id','marketing_addon_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_addons');
    }
};
