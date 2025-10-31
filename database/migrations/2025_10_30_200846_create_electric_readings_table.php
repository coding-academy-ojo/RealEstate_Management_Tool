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
        Schema::create('electric_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electric_service_id')
                ->constrained('electricity_services')
                ->cascadeOnDelete()
                ->comment('Parent electricity service for this reading entry');
            $table->decimal('imported_current', 10, 2)
                ->nullable()
                ->comment('القراءة الحالية للطاقة المستوردة من الشبكة');
            $table->decimal('imported_calculated', 10, 2)
                ->nullable()
                ->comment('القراءة المحتسبة للطاقة المستوردة (تفوتر على أساسها الخدمات غير الشمسية)');
            $table->decimal('produced_current', 10, 2)
                ->nullable()
                ->comment('القراءة الحالية للطاقة المُنتَجة/المُصدَّرة (للأنظمة الشمسية)');
            $table->decimal('produced_calculated', 10, 2)
                ->nullable()
                ->comment('القراءة المحتسبة للطاقة المُنتَجة (للأنظمة الشمسية)');
            $table->decimal('saved_energy', 10, 2)
                ->nullable()
                ->comment('الطاقة المخزّنة/المتبقية - يدخلها المستخدم في كل قراءة عند وجود نظام شمسي');
            $table->decimal('consumption_value', 10, 2)
                ->default(0)
                ->comment('قيمة الاستهلاك/الصافي - تحسب آلياً بناءً على منطق الأردن');
            $table->decimal('bill_amount', 10, 2)
                ->nullable()
                ->comment('Monthly invoice amount for this reading');
            $table->boolean('is_paid')
                ->default(false)
                ->comment('Indicates if the bill has been settled');
            $table->date('reading_date')
                ->nullable()
                ->comment('Date the reading was recorded');
            $table->string('meter_image')
                ->nullable()
                ->comment('Path to reference photo of the meter reading');
            $table->string('bill_image')
                ->nullable()
                ->comment('Path to supporting bill or document image');
            $table->text('notes')
                ->nullable()
                ->comment('Additional remarks for this reading');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_readings');
    }
};
