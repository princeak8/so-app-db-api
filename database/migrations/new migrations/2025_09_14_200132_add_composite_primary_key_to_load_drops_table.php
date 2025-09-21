<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function primaryKeyExists($tableName, $constraintName = null): bool
    {
        $constraintName = $constraintName ?: $tableName . '_pkey';
        
        $result = DB::select("
            SELECT 1 FROM information_schema.table_constraints 
            WHERE table_name = ? 
            AND constraint_type = 'PRIMARY KEY'
            AND constraint_name = ?
        ", [$tableName, $constraintName]);
        
        return !empty($result);
    }

    public function up(): void
    {
        Schema::table('load_drops', function (Blueprint $table) {
            if ($this->primaryKeyExists('load_drops')) {
                $table->dropPrimary(); 
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
