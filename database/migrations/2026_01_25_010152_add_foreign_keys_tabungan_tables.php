<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===============================
        // TABUNGAN UMRAH
        // ===============================
        Schema::table('tabungan_umrah', function (Blueprint $table) {
            $table->foreign('jamaah_id')
                ->references('id')->on('jamaah')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // ===============================
        // TABUNGAN TRANSAKSI
        // ===============================
        Schema::table('tabungan_transaksi', function (Blueprint $table) {
            $table->foreign('tabungan_id')
                ->references('id')->on('tabungan_umrah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // TABUNGAN TOPUPS
        // ===============================
        Schema::table('tabungan_topups', function (Blueprint $table) {
            $table->foreign('tabungan_id')
                ->references('id')->on('tabungan_umrah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // BUKTI SETORAN
        // ===============================
        Schema::table('bukti_setoran', function (Blueprint $table) {
            $table->foreign('tabungan_transaksi_id')
                ->references('id')->on('tabungan_transaksi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bukti_setoran', fn (Blueprint $t) =>
            $t->dropForeign(['tabungan_transaksi_id'])
        );

        Schema::table('tabungan_topups', fn (Blueprint $t) =>
            $t->dropForeign(['tabungan_id'])
        );

        Schema::table('tabungan_transaksi', fn (Blueprint $t) =>
            $t->dropForeign(['tabungan_id'])
        );

        Schema::table('tabungan_umrah', fn (Blueprint $t) =>
            $t->dropForeign(['jamaah_id'])
        );
    }
};
