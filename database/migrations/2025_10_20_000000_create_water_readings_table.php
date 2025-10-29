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
        Schema::create('water_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_service_id')->constrained()->cascadeOnDelete();
            $table->decimal('current_reading', 10, 2);
            $table->decimal('consumption_value', 10, 2)->nullable();
            $table->decimal('bill_amount', 10, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('reading_date')->nullable();
            $table->string('meter_image')->nullable();
            $table->string('bill_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_readings');
    }
};
