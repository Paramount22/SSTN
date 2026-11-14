<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Podujatia (semináre, konferencie, exkurzie, valné zhromaždenia)
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 20)->default('sstn');   // see Event::TYPES
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->string('date_text')->nullable();       // e.g. "8. a 9. apríla 2025" (overrides formatted dates)
            $table->string('location')->nullable();
            $table->string('organizers')->nullable();
            $table->text('intro')->nullable();             // "Vážení členovia ZSVTS a SSTN, ..."
            $table->text('body')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'starts_on']);
        });

        // Pozvánky, programy, prezentácie, prihlášky (PDF / DOC)
        Schema::create('event_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('path');                        // relative to the "public" disk, e.g. akcie/2026/pozvanka.pdf
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Editovateľné texty stránky (poslanie, členstvo, kontakt, ...)
        Schema::create('text_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('type', 10)->default('text');   // text | list | html
            $table->text('value')->nullable();
            $table->string('hint')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Členské príspevky
        Schema::create('membership_fees', function (Blueprint $table) {
            $table->id();
            $table->string('kind');
            $table->decimal('price', 8, 2);
            $table->unsignedSmallInteger('votes')->default(1);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Odkazy: partneri, pätička, videá, dokumenty na stiahnutie
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('group', 20);                   // see Link::GROUPS
            $table->string('label');
            $table->string('url');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
        Schema::dropIfExists('membership_fees');
        Schema::dropIfExists('text_blocks');
        Schema::dropIfExists('event_attachments');
        Schema::dropIfExists('events');
    }
};
