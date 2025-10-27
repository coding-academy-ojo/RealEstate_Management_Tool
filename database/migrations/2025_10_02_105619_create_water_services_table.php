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
        Schema::create('water_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('registration_number');
            $table->string('iron_number')->nullable();
            $table->decimal('previous_reading', 10, 2)->nullable();
            $table->decimal('current_reading', 10, 2)->nullable();
            $table->date('reading_date')->nullable();
            $table->string('invoice_file')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_services');
    }
};
