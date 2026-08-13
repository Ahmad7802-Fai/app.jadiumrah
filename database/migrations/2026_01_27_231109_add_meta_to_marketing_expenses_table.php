<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ Tambah kolom (AMAN)
        Schema::table('marketing_expenses', function (Blueprint $table) {

            if (!Schema::hasColumn('marketing_expenses', 'created_by')) {
                $table->unsignedBigInteger('created_by')
                      ->nullable()
                      ->after('tanggal');
            }

            if (!Schema::hasColumn('marketing_expenses', 'catatan')) {
                $table->text('catatan')
                      ->nullable()
                      ->after('created_by');
            }
        });

        // 2️⃣ Tambah FK (CEK MANUAL)
        $fkExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'marketing_expenses')
            ->where('COLUMN_NAME', 'created_by')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if (!$fkExists) {
            Schema::table('marketing_expenses', function (Blueprint $table) {
                $table->foreign('created_by')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('marketing_expenses', function (Blueprint $table) {

            if (Schema::hasColumn('marketing_expenses', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            if (Schema::hasColumn('marketing_expenses', 'catatan')) {
                $table->dropColumn('catatan');
            }
        });
    }
};
