<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketing_expenses', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('sumber_id');

            $table->string('nama_campaign')->nullable();

            $table->enum('platform', [
                'facebook',
                'instagram',
                'tiktok',
                'google',
                'offline',
            ])->nullable();

            $table->integer('biaya');
            $table->date('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_expenses');
    }
};
