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
        Schema::create('power_data', function (Blueprint $table) {
            $table->id(); // auto-increment, not used as primary for hypertable
            $table->unsignedBigInteger('power_station_id');
            $table->decimal('load', 10, 4);
            $table->decimal('frequency', 8, 4)->nullable();
            $table->timestampTz('captured_at');
            $table->timestampsTz();

            $table->primary(['id', 'captured_at']);
        
            // $table->foreign('power_station_id');
        
            $table->index('power_station_id');
            $table->index('captured_at');
        });

        // Convert to TimescaleDB hypertable with 1-day chunks
        DB::statement("
            SELECT create_hypertable('power_data', 'captured_at', 
                chunk_time_interval => INTERVAL '1 day',
                create_default_indexes => FALSE
            )
        ");
        
        // Add optimized composite indexes for time-series queries
        DB::statement("CREATE INDEX idx_power_data_station_time ON power_data (power_station_id, captured_at DESC)");
        DB::statement("CREATE INDEX idx_power_data_time_station ON power_data (captured_at DESC, power_station_id)");
        DB::statement("CREATE INDEX idx_power_data_load_time ON power_data (load, captured_at DESC) WHERE load > 0");
        
        // Add data retention policy (keep data for 90 days)
        DB::statement("SELECT add_retention_policy('power_data', INTERVAL '90 days')");
        
        // Enable compression for chunks older than 7 days
        DB::statement("
            ALTER TABLE power_data SET (
                timescaledb.compress,
                timescaledb.compress_segmentby = 'power_station_id'
            )
        ");
        DB::statement("SELECT add_compression_policy('power_data', INTERVAL '7 days')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_data');
    }
};
