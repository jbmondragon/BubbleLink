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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pending_shop_name')->nullable()->after('owner_registration_reviewed_at');
            $table->string('pending_shop_address')->nullable()->after('pending_shop_name');
            $table->string('pending_shop_contact_number')->nullable()->after('pending_shop_address');
            $table->string('pending_shop_description')->nullable()->after('pending_shop_contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pending_shop_name',
                'pending_shop_address',
                'pending_shop_contact_number',
                'pending_shop_description',
            ]);
        });
    }
};
