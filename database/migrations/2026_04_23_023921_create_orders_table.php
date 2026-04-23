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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('shop_service_id');

            $table->string('service_mode')->nullable()->comment('pickup_only, delivery_only, both');

            $table->string('pickup_address')->nullable();
            $table->string('delivery_address')->nullable();

            $table->decimal('weight', 10, 2)->nullable();

            $table->timestamp('pickup_datetime')->nullable();
            $table->timestamp('delivery_datetime')->nullable();

            $table->decimal('total_price', 10, 2);

            $table->string('status')->comment('pending, accepted, awaiting_dropoff, rejected, in_progress, completed');

            $table->string('payment_method')->nullable()->comment('gcash, cash');
            $table->string('payment_status')->nullable()->comment('paid, unpaid');

            $table->timestamp('created_at')->nullable();

            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
            $table->foreign('shop_service_id')->references('id')->on('shop_services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
