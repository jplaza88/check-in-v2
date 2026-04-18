<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->string('street1');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code');
            $table->string('zip_code_4');
            $table->string('country');
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->timestampsTz();

            $table->unique(['street1', 'city', 'state', 'zip_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
