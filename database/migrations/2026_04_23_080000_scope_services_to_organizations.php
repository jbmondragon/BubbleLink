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
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique('services_name_unique');
            $table->unsignedInteger('organization_id')->nullable()->after('id');
        });

        $services = DB::table('services')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        foreach ($services as $service) {
            $organizationIds = DB::table('shop_services')
                ->join('shops', 'shops.id', '=', 'shop_services.shop_id')
                ->where('shop_services.service_id', $service->id)
                ->orderBy('shops.organization_id')
                ->distinct()
                ->pluck('shops.organization_id')
                ->values();

            if ($organizationIds->isEmpty()) {
                continue;
            }

            DB::table('services')
                ->where('id', $service->id)
                ->update(['organization_id' => $organizationIds->first()]);

            foreach ($organizationIds->slice(1) as $organizationId) {
                $newServiceId = DB::table('services')->insertGetId([
                    'organization_id' => $organizationId,
                    'name' => $service->name,
                ]);

                $shopIds = DB::table('shops')
                    ->where('organization_id', $organizationId)
                    ->pluck('id');

                DB::table('shop_services')
                    ->where('service_id', $service->id)
                    ->whereIn('shop_id', $shopIds)
                    ->update(['service_id' => $newServiceId]);
            }
        }

        Schema::table('services', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
            $table->unique(['organization_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('services') && Schema::hasColumn('services', 'organization_id')) {
            $duplicateServices = DB::table('services')
                ->select('name')
                ->groupBy('name')
                ->havingRaw('count(*) > 1')
                ->pluck('name');

            foreach ($duplicateServices as $name) {
                $services = DB::table('services')
                    ->where('name', $name)
                    ->orderBy('id')
                    ->get(['id']);

                $primaryServiceId = $services->first()->id;

                foreach ($services->slice(1) as $service) {
                    DB::table('shop_services')
                        ->where('service_id', $service->id)
                        ->update(['service_id' => $primaryServiceId]);

                    DB::table('services')->where('id', $service->id)->delete();
                }
            }

            Schema::table('services', function (Blueprint $table) {
                if (Schema::hasIndex('services', 'services_organization_id_name_unique')) {
                    $table->dropUnique('services_organization_id_name_unique');
                }
                
                // Check if FK exists before dropping
                $fkExists = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services' AND COLUMN_NAME = 'organization_id' AND REFERENCED_TABLE_NAME IS NOT NULL"))->isNotEmpty();
                if ($fkExists) {
                    $table->dropForeign(['organization_id']);
                }
                
                $table->dropColumn('organization_id');
                $table->unique('name');
            });
        }
    }
};
