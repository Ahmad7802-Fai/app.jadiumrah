<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('airlines', function (Blueprint $table) {
            $table->id(); // int auto increment

            $table->string('code', 5)->unique();
            $table->string('name', 100);
            $table->string('country', 50)->nullable();

            $table->boolean('is_active')->default(true);

            // sesuai schema lama: timestamp nullable
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airlines');
    }
};
