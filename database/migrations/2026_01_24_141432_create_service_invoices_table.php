<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('id_client');

            $table->string('nomor_invoice', 100)->unique();

            // MySQL schema lama pakai curdate()
            $table->date('tanggal');


            $table->date('jatuh_tempo')->nullable();

            $table->decimal('total', 20, 2)->default(0);

            $table->enum('status', [
                'draft',
                'pending',
                'paid',
                'canceled',
            ])->default('draft');

            $table->text('catatan')->nullable();

            // schema lama
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('id_client');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoices');
    }
};
