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
        Schema::table('load_drops', function (Blueprint $table) {
            $table->dropPrimary(); 
            $table->primary(['id', 'time_of_drop']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('load_drops', function (Blueprint $table) {
            //
        });
    }
};
