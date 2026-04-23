<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->unsignedInteger('shop_id')->nullable()->after('organization_id');
            $table->foreign('shop_id')->references('id')->on('shops')->nullOnDelete();
        });

        $shopIdsByOrganization = DB::table('shops')
            ->orderBy('id')
            ->get(['id', 'organization_id'])
            ->groupBy('organization_id')
            ->map(fn ($shops) => $shops->first()->id);

        DB::table('memberships')
            ->whereIn('role', ['manager', 'staff'])
            ->orderBy('id')
            ->get(['id', 'organization_id'])
            ->each(function ($membership) use ($shopIdsByOrganization) {
                $shopId = $shopIdsByOrganization->get($membership->organization_id);

                if ($shopId) {
                    DB::table('memberships')
                        ->where('id', $membership->id)
                        ->update(['shop_id' => $shopId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};
