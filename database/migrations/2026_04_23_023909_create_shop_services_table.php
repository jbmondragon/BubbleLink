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
        Schema::create('shop_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('service_id');
            $table->decimal('price', 10, 2);

            $table->unique(['shop_id', 'service_id']);

            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_services');
    }
};
