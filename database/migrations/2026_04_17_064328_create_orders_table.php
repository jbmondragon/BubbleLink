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
            $table->bigIncrements('id'); // auto increment
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->enum('service_mode', ['pickup only', 'delivery only', 'both']);
            $table->string('pickup_address')->nullable();
            $table->string('delivery_address')->nullable();
            $table->dateTime('pickup_datetime')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'in-progress', 'completed'])->default('pending');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
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
