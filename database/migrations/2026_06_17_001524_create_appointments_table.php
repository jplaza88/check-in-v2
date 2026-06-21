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
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestampTz('scheduled_for');
            $table->string('drivers_name');
            $table->string('drivers_cellphone');
            $table->string('status')->default('scheduled');
            $table->timestampTz('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestampTz('claimed_at')->nullable();
            $table->string('claimed_via')->nullable();

            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['location_id', 'scheduled_for']);
            $table->index(['drivers_cellphone', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
