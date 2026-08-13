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

            /* ===============================
             | BASIC COMPANY INFO
             =============================== */
            $table->string('name'); // Nama PT resmi
            $table->string('brand_name')->nullable(); // Nama brand (invoice / marketing)

            /* ===============================
             | LOGO & BRANDING
             =============================== */
            $table->string('logo')->nullable(); // logo utama
            $table->string('logo_invoice')->nullable(); // logo khusus invoice
            $table->string('logo_bw')->nullable(); // logo hitam putih

            /* ===============================
             | CONTACT INFO
             =============================== */
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            /* ===============================
             | ADDRESS
             =============================== */
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            /* ===============================
             | LEGAL / TAX
             =============================== */
            $table->string('npwp')->nullable();
            $table->string('npwp_name')->nullable();
            $table->text('npwp_address')->nullable();

            /* ===============================
             | BANK ACCOUNT (INVOICE)
             =============================== */
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();

            /* ===============================
             | DOCUMENT SETTINGS
             =============================== */
            $table->text('invoice_footer')->nullable();
            $table->text('letter_footer')->nullable();

            /* ===============================
             | SIGNATURE
             =============================== */
            $table->string('signature_name')->nullable();
            $table->string('signature_position')->nullable();

            /* ===============================
             | STATUS
             =============================== */
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
