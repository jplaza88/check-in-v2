<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', static function (Blueprint $table): void {
            $table->id();
            $table->string('short_name', 2)->unique();
            $table->string('name', 150)->unique();
            $table->string('phone_code', 10);
            $table->boolean('active');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
