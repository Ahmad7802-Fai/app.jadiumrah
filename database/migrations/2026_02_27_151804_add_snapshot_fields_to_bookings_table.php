<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('room_type')->nullable()->after('agent_id');
            $table->integer('qty')->nullable()->after('room_type');
            $table->decimal('price_per_person_snapshot', 15, 2)->nullable()->after('qty');
            $table->timestamp('expired_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'room_type',
                'qty',
                'price_per_person_snapshot',
                'expired_at',
            ]);
        });
    }
};