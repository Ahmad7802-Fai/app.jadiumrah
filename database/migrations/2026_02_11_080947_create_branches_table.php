<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Nama cabang
            $table->string('code')->unique(); // Kode unik cabang (HQ, BDG01, SBY01)

            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};