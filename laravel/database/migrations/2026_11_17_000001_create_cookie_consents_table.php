<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Proof of cookie consent (GDPR čl. 7 ods. 1): a random ID, the choice and when. No IP or browser data. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->uuid('consent_id')->index();
            $table->json('categories');
            $table->unsignedSmallInteger('version');
            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};
