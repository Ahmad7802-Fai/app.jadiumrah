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
       Schema::create('saving_accounts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('jamaah_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('account_number')->unique();

            $table->decimal('balance',15,2)->default(0);

            $table->enum('status',[
                'pending',
                'active',
                'closed'
            ])->default('pending');

            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_accounts');
    }
};
