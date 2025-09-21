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
            try {
                $table->dropPrimary(); 
            } catch (\Exception $e) {
                // Primary key doesn't exist, continue
                if (!str_contains($e->getMessage(), 'does not exist')) {
                    throw $e; // Re-throw if it's a different error
                }
            } 
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
