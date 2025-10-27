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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();

            $table->unsignedSmallInteger('sequence');      // 01, 02,...
            $table->string('code', 24)->unique();          // 1AM00301
            $table->string('name');
            $table->decimal('area_m2', 12, 2)->nullable();

            $table->boolean('has_building_permit')->nullable();
            $table->string('building_permit_file')->nullable(); // رخصة بناء
            $table->boolean('has_occupancy_permit')->nullable();
            $table->string('occupancy_permit_file')->nullable(); // إذن إشغال
            $table->boolean('has_profession_permit')->nullable();
            $table->string('profession_permit_file')->nullable(); // رخصة مهن

            $table->string('as_built_drawing')->nullable(); // ملف PDF أو AutoCAD

            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['site_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
