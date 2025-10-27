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
        Schema::create('electricity_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('registration_number');
            $table->decimal('previous_reading', 10, 2)->nullable();
            $table->decimal('current_reading', 10, 2)->nullable();
            $table->date('reading_date')->nullable();
            $table->string('reset_file')->nullable();
            $table->string('remarks')->nullable(); // مثال: Check with Hashim Zahari
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_services');
    }
};
