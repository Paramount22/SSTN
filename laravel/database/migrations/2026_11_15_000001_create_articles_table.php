<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Články s fotkou (reportáže zo seminárov, exkurzií, výstav)
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->date('published_on');
            $table->text('body')->nullable();
            $table->string('image_path')->nullable();      // relative to the "public" disk
            $table->string('image_alt')->nullable();
            $table->unsignedSmallInteger('image_width')->nullable();
            $table->unsignedSmallInteger('image_height')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'published_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
