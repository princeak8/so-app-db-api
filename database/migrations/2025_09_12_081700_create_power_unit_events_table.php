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
        Schema::create('power_unit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId("power_station_id");
            $table->foreignId("power_unit_id");
            $table->string('event', 100); // Limited length for better performance
            $table->decimal('load', 10, 4);
            $table->decimal('prev_load', 10, 4);
            $table->decimal('reference_load', 10, 4);
            $table->decimal('frequency', 8, 4);
            $table->timestampsTz();
            
            // Foreign key constraints
            // $table->foreign('power_station_id')->references('id')->on('power_stations')->onDelete('cascade');
            // $table->foreign('power_unit_id')->references('id')->on('power_units')->onDelete('cascade');

            $table->primary(['id', 'created_at']);
            
            // Basic indexes
            $table->index('power_station_id');
            $table->index('power_unit_id');
            $table->index('event');
            $table->index('created_at');
        });

        // Convert to TimescaleDB hypertable using created_at as time dimension
        DB::statement("
            SELECT create_hypertable('power_unit_events', 'created_at', 
                chunk_time_interval => INTERVAL '1 day',
                create_default_indexes => FALSE
            )
        ");
        
        // Add composite indexes for event queries
        DB::statement("CREATE INDEX idx_power_events_station_time ON power_unit_events (power_station_id, created_at DESC)");
        DB::statement("CREATE INDEX idx_power_events_unit_time ON power_unit_events (power_unit_id, created_at DESC)");
        DB::statement("CREATE INDEX idx_power_events_event_time ON power_unit_events (event, created_at DESC)");
        DB::statement("CREATE INDEX idx_power_events_time_event ON power_unit_events (created_at DESC, event)");
        
        // Events might need longer retention (1 year)
        DB::statement("SELECT add_retention_policy('power_unit_events', INTERVAL '1 year')");
        
        // Enable compression for events older than 30 days
        DB::statement("
            ALTER TABLE power_unit_events SET (
                timescaledb.compress,
                timescaledb.compress_segmentby = 'power_station_id, power_unit_id, event',
                timescaledb.compress_orderby = 'created_at DESC'
            )
        ");
        DB::statement("SELECT add_compression_policy('power_unit_events', INTERVAL '30 days')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_unit_events');
    }
};
