<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', static function (Blueprint $table) {
            $table->id();
            $table->string('short_name', 2)->nullable();
            $table->string('name', 150);
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['short_name', 'country_id']);
            $table->unique(['name', 'country_id']);
            $table->index(['name', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
