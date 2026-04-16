<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |-----------------------------------
        | USERS TABLE (Customers + Owners)
        |-----------------------------------
        */
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact_number')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        /*
        |-----------------------------------
        | SHOPS TABLE
        |-----------------------------------
        */
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address');
            $table->string('contact_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        /*
        |-----------------------------------
        | SERVICES TABLE
        |-----------------------------------
        */
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('shop_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('price', 10, 2);
            $table->unique(['shop_id', 'service_id']);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });

        /*
        |-----------------------------------
        | ORDERS TABLE
        |-----------------------------------
        */
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('service_id');
            $table->enum('service_mode', ['pickup_only', 'delivery_only', 'both']);
            $table->string('pickup_address')->nullable();
            $table->string('delivery_address')->nullable();
            $table->timestamp('pickup_datetime')->nullable();
            $table->timestamp('delivery_datetime')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'accepted', 'awaiting_dropoff', 'rejected', 'in_progress', 'completed']);
            $table->timestamp('created_at')->useCurrent();
            $table->enum('payment_method', ['gcash', 'cash']);
            $table->enum('payment_status', ['paid', 'unpaid']);

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });

        /*
        |-----------------------------------
        | ORDER STATUS HISTORY (OPTIONAL)
        |-----------------------------------
        */
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->string('status');
            $table->text('note')->nullable();

            $table->timestamps();
        });

        /*
        |-----------------------------------
        | REVIEWS TABLE (OPTIONAL)
        |-----------------------------------
        */
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');

            $table->tinyInteger('rating'); // 1–5
            $table->text('comment')->nullable();

            $table->timestamps();
        });

        /*
        |-----------------------------------
        | LARAVEL DEFAULT TABLES
        |-----------------------------------
        */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('shop_services');
        Schema::dropIfExists('services');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('users');
    }
};