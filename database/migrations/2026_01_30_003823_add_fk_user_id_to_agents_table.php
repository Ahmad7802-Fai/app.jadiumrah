<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {

            // Hapus FK lama kalau ada (jaga-jaga)
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            $table->foreign('user_id', 'agents_user_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // 🔥 user dihapus → agent ikut
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign('agents_user_id_foreign');
        });
    }
};
