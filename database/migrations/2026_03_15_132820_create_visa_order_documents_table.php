<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_order_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_order_id')->constrained('visa_orders')->cascadeOnDelete();
            $table->foreignId('visa_order_traveler_id')->nullable()->constrained('visa_order_travelers')->nullOnDelete();

            $table->enum('document_type', [
                'ktp',
                'kk',
                'passport',
                'photo',
                'ticket',
                'hotel_booking',
                'transport_booking',
                'other',
            ]);

            $table->string('document_name');
            $table->string('file_path');
            $table->string('file_disk')->default('public');
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('document_type');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_order_documents');
    }
};