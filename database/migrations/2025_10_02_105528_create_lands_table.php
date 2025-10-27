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
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();

            // Location Information (in order)
            $table->string('governorate');              // المحافظة
            $table->string('directorate');              // المديرية
            $table->string('directorate_number');       // رقم المديرية
            $table->string('village')->nullable();      // القرية
            $table->string('village_number')->nullable(); // رقم القرية
            $table->string('basin');                    // الحوض
            $table->string('basin_number');             // رقم الحوض
            $table->string('neighborhood')->nullable(); // الحي
            $table->string('neighborhood_number')->nullable(); // رقم الحي
            $table->string('plot_number');              // رقم القطعة
            $table->string('plot_key');                 // مفتاح القطعة

            // Area and other details
            $table->decimal('area_m2', 10, 2);          // مساحة القطعة
            $table->string('region')->nullable();       // REGION
            $table->string('zoning')->nullable();       // التنظيم
            $table->string('land_directorate')->nullable(); // مديرية الأراضي

            // Documents and media
            $table->string('ownership_doc')->nullable();   // سند الملكية (PDF/JPG)
            $table->string('site_plan')->nullable();       // مخطط الموقع
            $table->string('zoning_plan')->nullable();     // مخطط تنظيمي
            $table->string('photos')->nullable();          // صور الموقع (20 صورة)

            // Map location
            $table->text('map_location')->nullable();      // Full Google Maps URL
            $table->decimal('latitude', 10, 7)->nullable();  // Extracted latitude
            $table->decimal('longitude', 10, 7)->nullable(); // Extracted longitude

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};
