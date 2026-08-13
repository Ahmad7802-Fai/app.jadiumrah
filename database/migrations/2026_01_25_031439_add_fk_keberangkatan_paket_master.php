<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('keberangkatan', function (Blueprint $table) {
            $table->foreign('id_paket_master')
                ->references('id')
                ->on('paket_master')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('keberangkatan', function (Blueprint $table) {
            $table->dropForeign(['id_paket_master']);
        });
    }
};

