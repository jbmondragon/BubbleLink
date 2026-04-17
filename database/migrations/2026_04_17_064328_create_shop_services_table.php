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
            $table->bigIncrements('id'); // auto increment
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['shop_id', 'service_id']);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
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
