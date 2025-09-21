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
        // Additional time-series optimized indexes
        DB::statement("CREATE INDEX idx_load_drops_station_time ON load_drops(power_station_id, time_of_drop DESC)");
        DB::statement("CREATE INDEX idx_load_drops_time_station ON load_drops(time_of_drop DESC, power_station_id)");
        
        // Optional: Convert load_drops to hypertable if it contains time-series data
        // Uncomment the following lines if load_drops should also be a time-series table
        
        DB::statement("
            SELECT create_hypertable('load_drops', 'time_of_drop', 
                chunk_time_interval => INTERVAL '1 day',
                create_default_indexes => FALSE,
                if_not_exists => TRUE,
                migrate_data => TRUE
            )
        ");
        // DB::statement("SELECT add_retention_policy('load_drops', INTERVAL '180 days')");
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('load_drops', function (Blueprint $table) {
        //     //
        // });
        DB::statement("DROP INDEX IF EXISTS idx_load_drops_station_time");
        DB::statement("DROP INDEX IF EXISTS idx_load_drops_time_station");
    }
};
