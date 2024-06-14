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
        Schema::create('load_drops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_station_id')->references('id')->on('power_stations')->constrained();
            $table->decimal('float', 5, 2);
            $table->decimal('previous_load', 5, 2);
            $table->decimal('reference_load', 5, 2);
            $table->timestamp('time_of_drop');
            $table->string('calculation_type');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_drops');
    }
};
