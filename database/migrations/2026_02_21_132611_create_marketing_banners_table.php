<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_banners', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->string('subtitle')->nullable();

            $table->string('image');
            $table->string('mobile_image')->nullable();

            $table->string('link')->nullable();
            $table->enum('link_type', ['internal','external'])->default('internal');

            $table->string('page')->default('home'); 
            $table->string('position')->default('hero');

            $table->integer('sort_order')->default(0);

            $table->enum('status', [
                'draft',
                'published',
                'archived'
            ])->default('draft');

            $table->boolean('is_active')->default(true);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained('marketing_campaigns')
                ->nullOnDelete();

            $table->string('target_role')->nullable(); // agent, jamaah, public
            $table->foreignId('target_branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);

            $table->timestamps();

            $table->index(['page','position']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_banners');
    }
};