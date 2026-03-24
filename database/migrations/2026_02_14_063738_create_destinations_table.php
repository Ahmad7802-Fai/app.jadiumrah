<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();

            $table->string('country');              // Saudi Arabia, Turkey, UAE
            $table->string('city');                 // Mekkah, Istanbul, Dubai
            $table->enum('type', ['umrah', 'tour', 'transit'])
                  ->default('tour');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['country', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};