<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Basic Details
            $table->date('date_of_birth')->nullable()->after('full_name');
            
            // KYC Details
            $table->string('pan_number')->nullable()->unique()->after('email');
            
            // Address
            $table->text('address')->nullable()->after('pan_number');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('pincode')->nullable()->after('state');
            
            // Bank Details
            $table->string('account_holder_name')->nullable()->after('pincode');
            $table->string('bank_name')->nullable()->after('account_holder_name');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('account_number');
            
            // Investment Details
            $table->string('fund_name')->nullable()->after('ifsc_code');
            $table->enum('investment_type', ['SIP', 'Lump Sum'])->nullable()->after('fund_name');
            $table->decimal('investment_amount', 15, 2)->nullable()->after('investment_type');
            
            // Nominee Details
            $table->string('nominee_name')->nullable()->after('investment_amount');
            $table->string('nominee_relationship')->nullable()->after('nominee_name');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'pan_number',
                'address',
                'city',
                'state',
                'pincode',
                'account_holder_name',
                'bank_name',
                'account_number',
                'ifsc_code',
                'fund_name',
                'investment_type',
                'investment_amount',
                'nominee_name',
                'nominee_relationship',
            ]);
        });
    }
};
