<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            if (! Schema::hasColumn('services', 'shop_id')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->unsignedInteger('shop_id')->nullable()->after('id');
                });
            }

            if (Schema::hasColumn('services', 'organization_id')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->unsignedInteger('organization_id')->nullable()->change();
                });
            }

            if (! Schema::hasColumn('shops', 'owner_user_id')) {
                Schema::table('shops', function (Blueprint $table) {
                    $table->unsignedInteger('owner_user_id')->nullable()->after('id');
                });
            }

            if (Schema::hasColumn('shops', 'organization_id')) {
                Schema::table('shops', function (Blueprint $table) {
                    $table->unsignedInteger('organization_id')->nullable()->change();
                });
            }

            return;
        }

        // Drop memberships first (foreign keys to organizations, users, shops).
        Schema::dropIfExists('memberships');

        // Add shop_id to services and drop organization_id.
        if (! Schema::hasColumn('services', 'shop_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unsignedInteger('shop_id')->nullable()->after('id');
                $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('services', 'organization_id')) {
            // Check if FK still exists before trying to drop it
            $fkExists = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services' AND COLUMN_NAME = 'organization_id' AND REFERENCED_TABLE_NAME IS NOT NULL"))->isNotEmpty();
            if ($fkExists) {
                DB::statement('ALTER TABLE services DROP FOREIGN KEY services_organization_id_foreign');
            }
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('organization_id');
            });
        }

        // Add owner_user_id to shops and drop organization_id.
        if (! Schema::hasColumn('shops', 'owner_user_id')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->unsignedInteger('owner_user_id')->nullable()->after('id');
            });
        }

        DB::statement('ALTER TABLE shops MODIFY owner_user_id INT UNSIGNED NULL');

        $ownerForeignKeyExists = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shops' AND COLUMN_NAME = 'owner_user_id' AND REFERENCED_TABLE_NAME IS NOT NULL"))->isNotEmpty();
        if (! $ownerForeignKeyExists) {
            DB::statement('ALTER TABLE shops ADD CONSTRAINT shops_owner_user_id_foreign FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE');
        }

        if (Schema::hasColumn('shops', 'organization_id')) {
            $shopOrganizationForeignKeyExists = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'shops' AND COLUMN_NAME = 'organization_id' AND REFERENCED_TABLE_NAME IS NOT NULL"))->isNotEmpty();
            if ($shopOrganizationForeignKeyExists) {
                DB::statement('ALTER TABLE shops DROP FOREIGN KEY shops_organization_id_foreign');
            }

            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('organization_id');
            });
        }

        // Drop organizations last (no more FK references pointing to it).
        Schema::dropIfExists('organizations');
    }

    public function down(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('owner_user_id');
            $table->string('name');
            $table->timestamp('created_at')->nullable();
            $table->foreign('owner_user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn('owner_user_id');
            $table->unsignedInteger('organization_id')->nullable()->after('id');
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
            $table->unsignedInteger('organization_id')->nullable()->after('id');
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('shop_id')->nullable();
            $table->string('role');
            $table->timestamp('created_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('shop_id')->references('id')->on('shops')->nullOnDelete();
        });
    }
};
