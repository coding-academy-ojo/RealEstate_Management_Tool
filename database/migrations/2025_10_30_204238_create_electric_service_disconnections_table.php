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
        Schema::create('electric_service_disconnections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electric_service_id')
                ->constrained('electricity_services')
                ->cascadeOnDelete()
                ->comment('الخدمة الكهربائية المرتبطة بهذا الحدث');
            $table->date('disconnection_date')
                ->nullable()
                ->comment('تاريخ الفصل');
            $table->date('reconnection_date')
                ->nullable()
                ->comment('تاريخ الإرجاع');
            $table->text('reason')
                ->nullable()
                ->comment('سبب الفصل أو ملاحظات إضافية');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_service_disconnections');
    }
};
