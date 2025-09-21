<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set up additional retention policies with different schedules
        
        // Compress power_data older than 3 days (more aggressive)
        DB::statement("SELECT remove_compression_policy('power_data', true)");
        DB::statement("SELECT add_compression_policy('power_data', INTERVAL '3 days')");
        
        // Compress power_unit_data older than 7 days
        DB::statement("SELECT remove_compression_policy('power_unit_data', true)");
        DB::statement("SELECT add_compression_policy('power_unit_data', INTERVAL '3 days')");
        
        // Create job to refresh continuous aggregates manually if needed
        DB::statement("
            CREATE OR REPLACE FUNCTION refresh_continuous_aggregates()
            RETURNS VOID AS $$
            BEGIN
                CALL refresh_continuous_aggregate('power_data_hourly_agg', NOW() - INTERVAL '2 hours', NOW());
                CALL refresh_continuous_aggregate('power_data_daily_agg', NOW() - INTERVAL '2 days', NOW());
                CALL refresh_continuous_aggregate('power_unit_hourly_agg', NOW() - INTERVAL '2 hours', NOW());
            END;
            $$ LANGUAGE plpgsql;
        ");
        
        // Create maintenance function to manually run cleanup tasks
        DB::statement("
            CREATE OR REPLACE FUNCTION maintenance_cleanup()
            RETURNS TEXT AS $$
            DECLARE
                result TEXT;
            BEGIN
                -- Force compression on eligible chunks
                PERFORM compress_chunk(chunk_schema, chunk_name, if_not_compressed => true)
                FROM timescaledb_information.chunks
                WHERE hypertable_name IN ('power_data', 'power_unit_data', 'power_unit_events')
                AND range_end < NOW() - INTERVAL '1 day';
                
                result := 'Maintenance completed at ' || NOW();
                RETURN result;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP FUNCTION IF EXISTS refresh_continuous_aggregates()");
        DB::statement("DROP FUNCTION IF EXISTS maintenance_cleanup()");
    }
};
