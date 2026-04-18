<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_exceptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('type');
            $table->date('date');
            $table->time('open')->nullable();
            $table->time('close')->nullable();
            $table->boolean('is_closed');
            $table->string('reason')->nullable();
            $table->timestampsTz();

            $table->unique(['location_id', 'date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
