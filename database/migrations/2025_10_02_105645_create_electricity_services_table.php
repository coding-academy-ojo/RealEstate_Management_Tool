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
            $table->string('subscriber_name')->comment('Full name of the electricity service subscriber');
            $table->string('meter_number')->unique()->comment('Serial number of the electricity meter');
            $table->boolean('has_solar_power')->default(false)->comment('Indicates whether this service participates in solar/net-metering');
            $table->boolean('is_active')->default(true)->comment('توضح إذا كانت الخدمة فعّالة أو غير فعّالة');
            $table->enum('deactivation_reason', ['cancelled', 'meter_changed', 'merged', 'other'])
                ->nullable()
                ->comment('سبب تعطيل الخدمة في حال عدم الفعالية');
            $table->date('deactivation_date')->nullable()->comment('تاريخ تعطيل أو إلغاء الخدمة');
            $table->string('company_name');
            $table->string('registration_number');
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
