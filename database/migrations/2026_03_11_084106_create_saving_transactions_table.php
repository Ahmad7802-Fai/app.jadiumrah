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
        Schema::create('saving_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('saving_account_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type',[
                'deposit',
                'withdraw',
                'convert_booking'
            ]);

            $table->decimal('amount',15,2);

            $table->string('reference')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_transactions');
    }
};
