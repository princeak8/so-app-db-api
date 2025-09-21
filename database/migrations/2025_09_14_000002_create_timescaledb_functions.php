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
        // Create function to get latest reading per station
        DB::statement("
            CREATE OR REPLACE FUNCTION get_latest_power_readings(station_ids INTEGER[] DEFAULT NULL)
            RETURNS TABLE (
                power_station_id INTEGER,
                load DECIMAL,
                frequency DECIMAL,
                captured_at TIMESTAMPTZ,
                age_minutes INTEGER
            ) AS $$
            BEGIN
                RETURN QUERY
                SELECT DISTINCT ON (pd.power_station_id)
                    pd.power_station_id::INTEGER,
                    pd.load,
                    pd.frequency,
                    pd.captured_at,
                    EXTRACT(EPOCH FROM (NOW() - pd.captured_at))::INTEGER / 60 as age_minutes
                FROM power_data pd
                WHERE (station_ids IS NULL OR pd.power_station_id = ANY(station_ids))
                ORDER BY pd.power_station_id, pd.captured_at DESC;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Create function to calculate power station efficiency
        DB::statement("
            CREATE OR REPLACE FUNCTION calculate_station_efficiency(
                station_id INTEGER,
                start_time TIMESTAMPTZ,
                end_time TIMESTAMPTZ
            )
            RETURNS TABLE (
                avg_load DECIMAL,
                load_factor DECIMAL,
                frequency_stability DECIMAL,
                uptime_percentage DECIMAL
            ) AS $$
            BEGIN
                RETURN QUERY
                SELECT 
                    AVG(pd.load) as avg_load,
                    (AVG(pd.load) / NULLIF(MAX(pd.load), 0) * 100)::DECIMAL as load_factor,
                    (100 - STDDEV(pd.frequency))::DECIMAL as frequency_stability,
                    (COUNT(*) * 100.0 / EXTRACT(EPOCH FROM (end_time - start_time)) * 60)::DECIMAL as uptime_percentage
                FROM power_data pd
                WHERE pd.power_station_id = station_id
                AND pd.captured_at BETWEEN start_time AND end_time;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Create function to detect anomalies
        DB::statement("
            CREATE OR REPLACE FUNCTION detect_power_anomalies(
                station_id INTEGER,
                hours_back INTEGER DEFAULT 24,
                threshold_factor DECIMAL DEFAULT 2.0
            )
            RETURNS TABLE (
                captured_at TIMESTAMPTZ,
                load DECIMAL,
                frequency DECIMAL,
                load_zscore DECIMAL,
                frequency_zscore DECIMAL
            ) AS $$
            BEGIN
                RETURN QUERY
                WITH stats AS (
                    SELECT 
                        AVG(load) as avg_load,
                        STDDEV(load) as stddev_load,
                        AVG(frequency) as avg_frequency,
                        STDDEV(frequency) as stddev_frequency
                    FROM power_data
                    WHERE power_station_id = station_id
                    AND captured_at >= NOW() - (hours_back || ' hours')::INTERVAL
                )
                SELECT 
                    pd.captured_at,
                    pd.load,
                    pd.frequency,
                    ((pd.load - stats.avg_load) / NULLIF(stats.stddev_load, 0))::DECIMAL as load_zscore,
                    ((pd.frequency - stats.avg_frequency) / NULLIF(stats.stddev_frequency, 0))::DECIMAL as frequency_zscore
                FROM power_data pd, stats
                WHERE pd.power_station_id = station_id
                AND pd.captured_at >= NOW() - (hours_back || ' hours')::INTERVAL
                AND (
                    ABS(pd.load - stats.avg_load) > threshold_factor * stats.stddev_load
                    OR ABS(pd.frequency - stats.avg_frequency) > threshold_factor * stats.stddev_frequency
                )
                ORDER BY pd.captured_at DESC;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP FUNCTION IF EXISTS get_latest_power_readings(INTEGER[])");
        DB::statement("DROP FUNCTION IF EXISTS calculate_station_efficiency(INTEGER, TIMESTAMPTZ, TIMESTAMPTZ)");
        DB::statement("DROP FUNCTION IF EXISTS detect_power_anomalies(INTEGER, INTEGER, DECIMAL)");
    }
};