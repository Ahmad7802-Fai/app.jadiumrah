<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_order_travelers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_order_id')->constrained('visa_orders')->cascadeOnDelete();

            $table->string('full_name');
            $table->enum('gender', ['male', 'female'])->nullable();

            $table->string('relationship')->nullable();
            $table->boolean('is_main_applicant')->default(false);

            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->default('Indonesia');

            $table->string('nik', 30)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_issue_place')->nullable();

            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();

            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('full_name');
            $table->index('passport_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_order_travelers');
    }
};