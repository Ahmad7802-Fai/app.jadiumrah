<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('brand_name')->nullable();

            $table->string('logo')->nullable();
            $table->string('logo_invoice')->nullable();
            $table->string('logo_bw')->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            $table->string('npwp')->nullable();
            $table->string('npwp_name')->nullable();
            $table->text('npwp_address')->nullable();

            $table->text('invoice_footer')->nullable();
            $table->text('letter_footer')->nullable();

            $table->string('signature_name')->nullable();
            $table->string('signature_position')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};