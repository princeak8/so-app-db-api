<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('load_drops', function (Blueprint $table) {
            DB::statement("
                CREATE INDEX idx_time_of_drop ON load_drops(time_of_drop)
            ");
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
