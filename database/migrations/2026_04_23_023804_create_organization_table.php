<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('owner_user_id');
            $table->timestamp('created_at')->nullable();

            $table->foreign('owner_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
