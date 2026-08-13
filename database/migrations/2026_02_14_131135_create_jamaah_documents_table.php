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
        Schema::create('jamaah_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jamaah_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('document_type', [
                'passport',
                'visa',
                'ktp',
                'kk',
                'vaccine',
                'other'
            ]);

            $table->string('file_path');
            $table->date('expired_at')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['jamaah_id','document_type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaah_documents');
    }
};
