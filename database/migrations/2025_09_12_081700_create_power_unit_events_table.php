<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('power_unit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId("power_station_id");
            $table->foreignId("power_unit_id");
            $table->string("event");
            $table->decimal('load', 5, 2);
            $table->decimal('prev_load', 5, 2);
            $table->decimal('reference_load', 5, 2);
            $table->decimal('frequency', 4, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_unit_events');
    }
};
