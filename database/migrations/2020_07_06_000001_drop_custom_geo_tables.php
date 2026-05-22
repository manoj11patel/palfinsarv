<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

// Runs BEFORE nnjeim/world migrations (2020-07-07) so they can recreate the tables cleanly.
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop FK columns added to customers in prior migrations
        if (Schema::hasColumn('customers', 'country_id')) {
            Schema::table('customers', function ($table) {
                if ($this->foreignKeyExists('customers', 'customers_country_id_foreign')) {
                    $table->dropForeign(['country_id']);
                }
            });
        }
        if (Schema::hasColumn('customers', 'city_id')) {
            Schema::table('customers', function ($table) {
                if ($this->foreignKeyExists('customers', 'customers_city_id_foreign')) {
                    $table->dropForeign(['city_id']);
                }
            });
        }
        if (Schema::hasColumn('customers', 'state_id')) {
            Schema::table('customers', function ($table) {
                if ($this->foreignKeyExists('customers', 'customers_state_id_foreign')) {
                    $table->dropForeign(['state_id']);
                }
            });
        }

        // Drop custom tables (nnjeim will recreate them with full world data)
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Intentionally empty — this migration exists to hand off to nnjeim/world tables
    }

    private function foreignKeyExists(string $table, string $fkName): bool
    {
        try {
            $fks = \Illuminate\Support\Facades\DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
                [$table, $fkName]
            );
            return !empty($fks);
        } catch (\Throwable) {
            return false;
        }
    }
};
