<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {

            // 1️⃣ Tambah kode jamaah
            $table->string('jamaah_code')
                ->unique()
                ->after('id');

            // 2️⃣ Data personal penting
            $table->string('gender')
                ->nullable()
                ->after('nama_lengkap');

            $table->date('tanggal_lahir')
                ->nullable()
                ->after('gender');

            $table->string('tempat_lahir')
                ->nullable()
                ->after('tanggal_lahir');

            // 3️⃣ Status approval
            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending')
              ->after('is_active');

            // 4️⃣ Source channel
            $table->enum('source', [
                'offline',
                'branch',
                'agent',
                'website'
            ])->default('offline')
              ->after('branch_id');

            // 5️⃣ Soft delete
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        //
    }
};