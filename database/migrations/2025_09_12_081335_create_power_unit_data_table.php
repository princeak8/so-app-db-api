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
            $table->foreignId("power_data_id")->nullable();
            $table->foreignId("power_unit_id");
            $table->decimal('mw', 10, 4); // Megawatts with higher precision
            $table->decimal('kv', 8, 4);  // Kilovolts
            $table->decimal('a', 8, 4);   // Amperage
            $table->decimal('mx', 8, 4);  // Custom metric
            $table->decimal('frequency', 8, 4)->nullable(); // Frequency
            $table->timestampTz('captured_at');
            $table->timestampsTz();
            
            // Foreign key constraints
            // $table->foreign('power_station_id')->references('id')->on('power_stations')->onDelete('cascade');
            // $table->foreign('power_data_id')->references('id')->on('power_data')->onDelete('set null');
            // $table->foreign('power_unit_id')->references('id')->on('power_units')->onDelete('cascade');

            $table->primary(['id', 'captured_at']);
            
            // Basic indexes
            $table->index('power_station_id');
            $table->index('power_unit_id');
            $table->index('captured_at');
        });

        // Convert to TimescaleDB hypertable
        DB::statement("
            SELECT create_hypertable('power_unit_data', 'captured_at', 
                chunk_time_interval => INTERVAL '1 day',
                create_default_indexes => FALSE
            )
        ");
        
        // Add optimized composite indexes for common query patterns
        DB::statement("CREATE INDEX idx_power_unit_data_station_time ON power_unit_data (power_station_id, captured_at DESC)");
        DB::statement("CREATE INDEX idx_power_unit_data_unit_time ON power_unit_data (power_unit_id, captured_at DESC)");
        DB::statement("CREATE INDEX idx_power_unit_data_time_station ON power_unit_data (captured_at DESC, power_station_id)");
        DB::statement("CREATE INDEX idx_power_unit_data_mw_time ON power_unit_data (mw, captured_at DESC) WHERE mw > 0");
        
        // Add data retention policy (keep data for 90 days)
        DB::statement("SELECT add_retention_policy('power_unit_data', INTERVAL '90 days')");
        
        // Enable compression for older data (compress after 7 days)
        DB::statement("
            ALTER TABLE power_unit_data SET (
                timescaledb.compress,
                timescaledb.compress_segmentby = 'power_station_id, power_unit_id',
                timescaledb.compress_orderby = 'captured_at DESC'
            )
        ");
        DB::statement("SELECT add_compression_policy('power_unit_data', INTERVAL '7 days')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_unit_data');
    }
};
