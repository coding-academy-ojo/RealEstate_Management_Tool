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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('cluster_no')->default(1);

            // Governorates grouped by region
            // Region 1 (Capital): AM
            // Region 2 (North): IR, MF, AJ, JA
            // Region 3 (Middle): BA, ZA, MA
            // Region 4 (South): AQ, KA, TF, MN
            $table->enum('governorate', [
                'AM',  // Amman - Region 1
                'IR',  // Irbid - Region 2
                'MF',  // Mafraq - Region 2
                'AJ',  // Ajloun - Region 2
                'JA',  // Jerash - Region 2
                'BA',  // Balqa - Region 3
                'ZA',  // Zarqa - Region 3
                'MA',  // Madaba - Region 3
                'AQ',  // Aqaba - Region 4
                'KA',  // Karak - Region 4
                'TF',  // Tafileh - Region 4
                'MN'   // Ma'an - Region 4
            ]);

            // Region: 1=Capital, 2=North, 3=Middle, 4=South
            $table->unsignedTinyInteger('region')->comment('1=Capital, 2=North, 3=Middle, 4=South');

            $table->unsignedInteger('serial_no');
            $table->string('code', 16)->unique();   // Format: [Region][Governorate][Serial] e.g., 1AM002, 2IR015

            $table->string('name');
            $table->decimal('area_m2', 12, 2)->nullable();
            $table->string('zoning_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('other_documents')->nullable()->comment('Array of other document file paths');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['governorate', 'serial_no']);
            $table->index('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
