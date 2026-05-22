<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('aadhar_number')->nullable()->unique()->after('pan_number');
            $table->string('pan_file_path')->nullable()->after('aadhar_number');
            $table->string('aadhar_front_file_path')->nullable()->after('pan_file_path');
            $table->string('aadhar_back_file_path')->nullable()->after('aadhar_front_file_path');
            $table->string('passport_file_path')->nullable()->after('aadhar_back_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'aadhar_number',
                'pan_file_path',
                'aadhar_front_file_path',
                'aadhar_back_file_path',
                'passport_file_path',
            ]);
        });
    }
};
