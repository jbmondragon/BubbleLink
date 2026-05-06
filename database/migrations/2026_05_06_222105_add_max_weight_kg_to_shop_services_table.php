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
        Schema::table('shop_services', function (Blueprint $table) {
            $table->decimal('max_weight_kg', 5, 2)
                ->nullable()
                ->after('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_services', function (Blueprint $table) {
            $table->dropColumn('max_weight_kg');
        });
    }
};
