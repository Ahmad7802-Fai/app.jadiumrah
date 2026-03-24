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
        Schema::table('pakets', function (Blueprint $table) {

            $table->string('slug')->nullable()->after('code');
            $table->string('departure_city')->nullable()->after('slug');
            $table->integer('duration_days')->nullable()->after('return_date');
            $table->string('airline')->nullable()->after('duration_days');
            $table->integer('quota')->nullable()->after('airline');

            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();

            $table->boolean('is_published')->default(false)->after('is_active');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
