<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {

            $table->id();

            $table->string('code')->unique();

            $table->enum('type', ['fixed', 'percent']);
            $table->decimal('value', 15, 2);

            $table->decimal('max_discount', 15, 2)->nullable();

            $table->integer('quota')->nullable();
            $table->integer('used')->default(0);

            $table->timestamp('expired_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};