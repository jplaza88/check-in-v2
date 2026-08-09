<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->string('reference_number', 8)->unique();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestampTz('scheduled_for');
            $table->string('drivers_name');
            $table->string('drivers_cellphone');
            $table->string('locale', 2)->default('en');
            $table->string('status')->default('scheduled');
            $table->timestampTz('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestampTz('claimed_at')->nullable();
            $table->string('claimed_via')->nullable();
            // Stamped when the day-before reminder is queued, and the only thing
            // stopping the hourly command sending it twice. If rescheduling is
            // ever built, moving scheduled_for must null this or the booking
            // silently loses its reminder.
            $table->timestampTz('reminder_sent_at')->nullable();

            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['location_id', 'scheduled_for']);
            $table->index(['drivers_cellphone', 'created_at']);
            // Serves the driver history list, which filters by user and sorts
            // by scheduled_for. Covers plain user_id lookups as a leftmost prefix.
            $table->index(['user_id', 'scheduled_for']);
            // Serves the hourly reminder sweep, which filters on exactly this
            // pair. Neither index above works for it as a leftmost prefix.
            $table->index(['status', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
