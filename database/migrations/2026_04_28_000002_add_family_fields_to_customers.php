<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Anniversary date
            $table->date('anniversary_date')->nullable()->after('date_of_birth');
            
            // Spouse details
            $table->string('spouse_name')->nullable()->after('anniversary_date');
            $table->date('spouse_dob')->nullable()->after('spouse_name');
            
            // Child 1
            $table->string('child1_name')->nullable()->after('spouse_dob');
            $table->date('child1_dob')->nullable()->after('child1_name');
            
            // Child 2
            $table->string('child2_name')->nullable()->after('child1_dob');
            $table->date('child2_dob')->nullable()->after('child2_name');
            
            // Child 3
            $table->string('child3_name')->nullable()->after('child2_dob');
            $table->date('child3_dob')->nullable()->after('child3_name');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'anniversary_date',
                'spouse_name',
                'spouse_dob',
                'child1_name',
                'child1_dob',
                'child2_name',
                'child2_dob',
                'child3_name',
                'child3_dob',
            ]);
        });
    }
};
