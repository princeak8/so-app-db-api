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
        // Create continuous aggregate for hourly power data
        DB::statement("
            CREATE MATERIALIZED VIEW power_data_hourly_agg
            WITH (timescaledb.continuous) AS
            SELECT 
                power_station_id,
                time_bucket('1 hour', captured_at) AS time_bucket,
                AVG(load) as avg_load,
                MAX(load) as max_load,
                MIN(load) as min_load,
                STDDEV(load) as load_stddev,
                AVG(frequency) as avg_frequency,
                MAX(frequency) as max_frequency,
                MIN(frequency) as min_frequency,
                COUNT(*) as readings_count,
                first(load, captured_at) as first_load,
                last(load, captured_at) as last_load
            FROM power_data
            GROUP BY power_station_id, time_bucket
            WITH NO DATA
        ");

        // Add refresh policy for hourly aggregates
        DB::statement("
            SELECT add_continuous_aggregate_policy('power_data_hourly_agg',
                start_offset => INTERVAL '3 hours',
                end_offset => INTERVAL '30 minutes',
                schedule_interval => INTERVAL '30 minutes')
        ");

        // Create continuous aggregate for daily power data
        DB::statement("
            CREATE MATERIALIZED VIEW power_data_daily_agg
            WITH (timescaledb.continuous) AS
            SELECT 
                power_station_id,
                time_bucket('1 day', captured_at) AS time_bucket,
                AVG(load) as avg_load,
                MAX(load) as max_load,
                MIN(load) as min_load,
                STDDEV(load) as load_stddev,
                AVG(frequency) as avg_frequency,
                MAX(frequency) as max_frequency,
                MIN(frequency) as min_frequency,
                COUNT(*) as readings_count,
                SUM(load) as total_load
            FROM power_data
            GROUP BY power_station_id, time_bucket
            WITH NO DATA
        ");

        // Add refresh policy for daily aggregates
        DB::statement("
            SELECT add_continuous_aggregate_policy('power_data_daily_agg',
                start_offset => INTERVAL '3 days',
                end_offset => INTERVAL '1 hour',
                schedule_interval => INTERVAL '1 hour')
        ");

        // Create continuous aggregate for power unit performance
        DB::statement("
            CREATE MATERIALIZED VIEW power_unit_hourly_agg
            WITH (timescaledb.continuous) AS
            SELECT 
                power_station_id,
                power_unit_id,
                time_bucket('1 hour', captured_at) AS time_bucket,
                AVG(mw) as avg_mw,
                MAX(mw) as max_mw,
                MIN(mw) as min_mw,
                AVG(kv) as avg_kv,
                AVG(a) as avg_amperage,
                AVG(mx) as avg_mx,
                AVG(frequency) as avg_frequency,
                COUNT(*) as readings_count,
                -- Calculate efficiency metrics
                AVG(mw / NULLIF(kv * a * 1.732, 0)) as avg_power_factor
            FROM power_unit_data
            GROUP BY power_station_id, power_unit_id, time_bucket
            WITH NO DATA
        ");

        DB::statement("
            SELECT add_continuous_aggregate_policy('power_unit_hourly_agg',
                start_offset => INTERVAL '3 hours',
                end_offset => INTERVAL '30 minutes',
                schedule_interval => INTERVAL '30 minutes')
        ");

        // Create indexes on continuous aggregates for faster queries
        DB::statement("CREATE INDEX idx_power_data_hourly_agg_station_time ON power_data_hourly_agg (power_station_id, time_bucket DESC)");
        DB::statement("CREATE INDEX idx_power_data_daily_agg_station_time ON power_data_daily_agg (power_station_id, time_bucket DESC)");
        DB::statement("CREATE INDEX idx_power_unit_hourly_agg_unit_time ON power_unit_hourly_agg (power_unit_id, time_bucket DESC)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS power_data_hourly_agg");
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS power_data_daily_agg");
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS power_unit_hourly_agg");
    }
};