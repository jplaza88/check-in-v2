<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('type');
            $table->time('sunday_open')->nullable();
            $table->time('sunday_close')->nullable();
            $table->time('monday_open')->nullable();
            $table->time('monday_close')->nullable();
            $table->time('tuesday_open')->nullable();
            $table->time('tuesday_close')->nullable();
            $table->time('wednesday_open')->nullable();
            $table->time('wednesday_close')->nullable();
            $table->time('thursday_open')->nullable();
            $table->time('thursday_close')->nullable();
            $table->time('friday_open')->nullable();
            $table->time('friday_close')->nullable();
            $table->time('saturday_open')->nullable();
            $table->time('saturday_close')->nullable();
            $table->timestampsTz();

            $table->unique(['location_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
