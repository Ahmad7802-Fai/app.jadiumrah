<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id(); // bigint auto increment

            $table->string('phone', 20)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->nullable();
            $table->text('response')->nullable();

            // timestamp sesuai schema lama
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
