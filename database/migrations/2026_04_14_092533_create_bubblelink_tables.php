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
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('contact_number', 20)->nullable();
            $table->enum('role', ['customer', 'owner'])->default('customer');
            $table->rememberToken();
            $table->timestamps();
        });

        /*
        |-----------------------------------
        | SHOPS TABLE
        |-----------------------------------
        */
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('shop_name');
            $table->string('address');
            $table->string('contact_number', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        /*
        |-----------------------------------
        | SERVICES TABLE
        |-----------------------------------
        */
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');

            $table->string('service_name');
            $table->decimal('price_per_kg', 10, 2);
            $table->text('description')->nullable();

            $table->timestamps();
        });

        /*
        |-----------------------------------
        | ORDERS TABLE
        |-----------------------------------
        */
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');

            $table->foreignId('service_id')
                ->constrained('services')
                ->onDelete('cascade');

            $table->string('pickup_address')->nullable();
            $table->string('delivery_address')->nullable();

            $table->decimal('weight', 6, 2);
            $table->decimal('total_price', 10, 2);

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->dateTime('pickup_schedule')->nullable();
            $table->dateTime('delivery_schedule')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('services');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};