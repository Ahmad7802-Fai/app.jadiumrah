<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->enum('product_type', [
                'visa_only',
                'visa_bundle',
                'add_on',
            ])->default('visa_only');

            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('promo_price', 15, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            $table->json('features')->nullable();
            $table->json('requirements')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_products');
    }
};