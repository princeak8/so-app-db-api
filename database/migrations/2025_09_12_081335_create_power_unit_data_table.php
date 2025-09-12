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
        Schema::create('power_unit_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId("power_station_id");
            $table->foreignId("power_data_id");
            $table->foreignId("power_unit_id");
            $table->decimal('mw', 5, 2);
            $table->decimal('kv', 5, 2);
            $table->decimal('a', 5, 2);
            $table->decimal('mx', 5, 2);
            $table->decimal('frequency', 4, 2);
            $table->dateTime("captured_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_unit_data');
    }
};
