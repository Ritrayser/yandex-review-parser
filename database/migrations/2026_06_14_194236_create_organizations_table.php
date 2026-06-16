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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('yandex_url')->unique();
            $table->string('organization_id')->nullable();
            $table->string('name');
            $table->float('average_rating')->nullable();
            $table->integer('total_ratings')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->json('reviews_cache')->nullable();
            $table->timestamp('last_parsed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
