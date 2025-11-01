<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('electricity_companies')) {
            return;
        }

        Schema::table('electricity_companies', function (Blueprint $table): void {
            if (!Schema::hasColumn('electricity_companies', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('electricity_companies')) {
            return;
        }

        Schema::table('electricity_companies', function (Blueprint $table): void {
            if (Schema::hasColumn('electricity_companies', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
