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
        Schema::create('re_innovations', function (Blueprint $table) {
            $table->id();
            // Polymorphic relationship - can belong to Site, Land, or Building
            $table->morphs('innovatable'); // Creates innovatable_id and innovatable_type
            $table->date('date'); // Date
            $table->decimal('cost', 15, 2); // Cost
            $table->string('name'); // Name
            $table->text('description')->nullable(); // Description
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('re_innovations');
    }
};
