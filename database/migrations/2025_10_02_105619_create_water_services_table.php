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
            $table->string('meter_owner_name');
            $table->string('registration_number');
            $table->string('iron_number')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true)->comment('توضح إذا كانت الخدمة فعّالة أو غير فعّالة');
            $table->enum('deactivation_reason', ['cancelled', 'meter_changed', 'merged', 'other'])
                ->nullable()
                ->comment('سبب تعطيل الخدمة في حال عدم الفعالية');
            $table->date('deactivation_date')->nullable()->comment('تاريخ تعطيل أو إلغاء الخدمة');
            $table->string('initial_meter_image')->nullable();
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
