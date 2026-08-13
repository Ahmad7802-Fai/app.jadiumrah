<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('marketing_expenses', function (Blueprint $table) {
            // Catatan internal
            $table->text('catatan')->nullable()->after('tanggal');

            // Audit
            $table->unsignedBigInteger('created_by')->nullable()->after('catatan');

            // Index ringan
            $table->index('platform');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_expenses', function (Blueprint $table) {
            $table->dropColumn(['catatan', 'created_by']);
        });
    }
};
