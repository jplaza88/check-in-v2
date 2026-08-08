<?php

declare(strict_types=1);

use App\Models\CheckIn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_history', function (Blueprint $table): void {
            $table->id();
            $table->morphs('recordable');
            $table->string('event');
            $table->string('subject')->nullable();
            $table->string('channel')->nullable();
            $table->string('locale', 5)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('context')->nullable();

            $table->timestampsTz();

            // Serves the timeline: every read is "this record, oldest first".
            $table->index(['recordable_type', 'recordable_id', 'created_at']);
        });

        /*
         * The morph map only ever named Appointment, so check-in purchase orders
         * were written with the class name while bookings used the alias. Naming
         * CheckIn in the map (AppServiceProvider) fixes new rows; this realigns
         * the ones already stored. A no-op on a freshly migrated database.
         */
        DB::table('purchase_orders')
            ->where('purchasable_type', CheckIn::class)
            ->update(['purchasable_type' => 'check_in']);
    }

    public function down(): void
    {
        Schema::dropIfExists('record_history');
    }
};
