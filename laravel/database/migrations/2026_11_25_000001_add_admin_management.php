<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Set while an invited admin has not chosen a password yet; cleared on acceptance.
            $table->timestamp('invitation_sent_at')->nullable()->after('is_admin');
            // A deactivated account keeps its data but can no longer log in.
            $table->timestamp('deactivated_at')->nullable()->after('invitation_sent_at');
        });

        // Own table, so an ordinary 60-minute password reset token can never be
        // redeemed on the invitation page, which accepts tokens for 7 days.
        Schema::create('admin_invitation_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Who added, removed or reactivated which admin. Names are copied, so the
        // record stays readable after an account is deleted.
        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_name');
            $table->string('subject_email');
            $table->string('action', 32);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activities');
        Schema::dropIfExists('admin_invitation_tokens');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['invitation_sent_at', 'deactivated_at']);
        });
    }
};
