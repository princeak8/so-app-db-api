<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('power_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId("power_station_id");
            $table->string('name');
            $table->string('identifier');
            $table->timestamps();
        });
        Artisan::call("db:seed", ["--class" => "PowerUnits"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_units');
    }
};
