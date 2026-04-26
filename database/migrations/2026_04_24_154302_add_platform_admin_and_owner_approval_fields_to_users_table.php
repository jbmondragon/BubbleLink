<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_platform_admin')->default(false)->after('contact_number');
            $table->string('owner_registration_status')->nullable()->after('is_platform_admin');
            $table->unsignedInteger('approved_by_user_id')->nullable()->after('owner_registration_status');
            $table->timestamp('owner_registration_reviewed_at')->nullable()->after('approved_by_user_id');

            $table->foreign('approved_by_user_id')->references('id')->on('users')->nullOnDelete();
        });

        // if (! DB::table('users')->where('is_platform_admin', true)->exists()) {
        //     DB::table('users')->insert([
        //         'name' => 'System Admin',
        //         'email' => 'admin@bubblelink.test',
        //         'email_verified_at' => now(),
        //         'password' => bcrypt('password'),
        //         'remember_token' => Str::random(10),
        //         'contact_number' => '09170000007',
        //         'is_platform_admin' => true,
        //         'owner_registration_status' => null,
        //         'approved_by_user_id' => null,
        //         'owner_registration_reviewed_at' => null,
        //         'created_at' => now(),
        //     ]);
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by_user_id']);
            $table->dropColumn([
                'is_platform_admin',
                'owner_registration_status',
                'approved_by_user_id',
                'owner_registration_reviewed_at',
            ]);
        });
    }
};
