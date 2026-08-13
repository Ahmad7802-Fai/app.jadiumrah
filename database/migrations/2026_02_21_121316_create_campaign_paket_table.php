<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_paket', function (Blueprint $table) {

            $table->id();

            $table->foreignId('marketing_campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('paket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'marketing_campaign_id',
                'paket_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_paket');
    }
};