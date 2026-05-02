<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkin_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('location_id')->constrained('locations');
            $table->integer('day_of_week');
            $table->time('open_time');
            $table->time('close_time');
            $table->timestampsTz();

            $table->unique(['location_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkin_schedules');
    }
};
