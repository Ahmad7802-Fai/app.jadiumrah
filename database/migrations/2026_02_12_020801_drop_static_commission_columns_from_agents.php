<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('agents', function (Blueprint $table) {
        $table->dropColumn([
            'komisi_persen',
            'komisi_manual',
            'komisi_affiliate',
        ]);
    });
}

public function down(): void
{
    Schema::table('agents', function (Blueprint $table) {
        $table->decimal('komisi_persen', 5, 2)->default(0);
        $table->decimal('komisi_manual', 5, 2)->default(0);
        $table->decimal('komisi_affiliate', 5, 2)->default(0);
    });
}
};
