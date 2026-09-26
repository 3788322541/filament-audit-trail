<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // The record that was changed (polymorphic).
            $table->string('auditable_type');
            $table->string('auditable_id');
            // Redundant display name so the record is identifiable even after deletion.
            $table->string('auditable_name')->nullable();

            // created | updated | deleted | restored | attached | detached
            $table->string('event', 32);

            // Field-level diff.
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Who made the change (polymorphic actor).
            $table->string('actor_type')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();

            // Request context.
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->json('tags')->nullable();

            // Reserved for the Pro tier (multi-team scoping / tamper-evidence).
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('hash', 64)->nullable();

            $table->timestamp('created_at');

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['actor_type', 'actor_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
