<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('address_id')->constrained('addresses')->cascadeOnDelete();
            $table->integer('max_distance_allowed');
            $table->string('name');
            $table->string('abbreviation')->unique();
            $table->string('timezone');
            $table->string('phone');
            $table->string('phone_ext')->nullable();
            $table->string('email');
            $table->boolean('is_active');
            $table->boolean('is_checkins_enabled');
            $table->boolean('is_appointments_enabled');
            $table->json('config')->nullable();

            $table->timestampsTz();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
