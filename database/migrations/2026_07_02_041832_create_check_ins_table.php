<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->string('reference_number', 8)->unique();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status');
            $table->string('erp_status');
            $table->string('customer');
            $table->string('destination_city');
            $table->string('destination_state');
            $table->string('destination_country');
            $table->string('truck_name');
            $table->string('truck_plate');
            $table->string('truck_color')->nullable();
            $table->string('truck_plate_state')->nullable();
            $table->string('truck_plate_country')->nullable();
            $table->string('trailer_plate');
            $table->string('trailer_plate_state')->nullable();
            $table->string('trailer_plate_country')->nullable();
            $table->string('trailer_chute')->nullable();
            $table->integer('empty_weight_lbs')->nullable();
            $table->string('drivers_name');
            $table->string('drivers_cellphone');
            $table->string('drivers_email')->nullable();
            // Stored encrypted at rest (App\Casts\Encrypted); ciphertext needs text width.
            $table->text('drivers_license_number');
            $table->date('drivers_license_expiration_date')->nullable();
            $table->string('drivers_license_state')->nullable();
            $table->string('drivers_license_country')->nullable();
            $table->text('loading_instructions')->nullable();
            $table->string('locale', 2)->default('en');
            $table->timestampTz('claimed_at')->nullable();
            $table->string('claimed_via')->nullable();

            $table->timestampsTz();
            $table->softDeletes();

            $table->index('location_id');
            $table->index(['drivers_cellphone', 'created_at']);
            // Serves the driver history list, which filters by user and sorts
            // by created_at. Covers plain user_id lookups as a leftmost prefix.
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
