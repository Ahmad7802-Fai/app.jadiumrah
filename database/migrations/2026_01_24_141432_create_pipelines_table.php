<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pipelines', function (Blueprint $table) {
            $table->id(); // int auto increment

            $table->string('tahap', 100);
            $table->integer('urutan');

            $table->boolean('aktif')->default(true);

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // unique sesuai schema lama
            $table->unique(['tahap', 'aktif'], 'uniq_pipeline_tahap_aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipelines');
    }
};
