<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->morphs('purchasable');
            $table->string('number');
            $table->timestampsTz();

            $table->unique(['purchasable_type', 'purchasable_id', 'number']);
            $table->index('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
