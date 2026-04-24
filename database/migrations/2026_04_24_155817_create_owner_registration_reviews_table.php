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
        Schema::create('owner_registration_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_owner_user_id');
            $table->unsignedInteger('platform_admin_user_id');
            $table->string('action');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->timestamps();

            $table->foreign('shop_owner_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('platform_admin_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_registration_reviews');
    }
};
