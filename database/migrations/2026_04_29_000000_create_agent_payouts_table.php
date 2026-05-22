<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->string('month'); // Can be integer or string (e.g., 'April')
            $table->year('year');
            $table->integer('total_policies')->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('commission', 15, 2);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agent_profiles')->onDelete('cascade');
            $table->unique(['agent_id', 'month', 'year']); // Prevent duplicate payouts
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payouts');
    }
};
