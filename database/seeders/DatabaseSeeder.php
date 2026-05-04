<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \DB::table('users')->insert([
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000001', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 2, 'name' => 'Bob Customer', 'email' => 'bob@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000002', 'is_platform_admin' => false, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 3, 'name' => 'System Admin', 'email' => 'admin@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000003', 'is_platform_admin' => true, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
        ]);

        \DB::table('shops')->insert([
            ['id' => 1, 'owner_user_id' => 1, 'shop_name' => 'QuickClean Manila', 'address' => 'Manila City', 'contact_number' => '0287001001', 'description' => 'Pickup, delivery, and same-day folding for busy households.', 'created_at' => now()],
        ]);

        \DB::table('services')->insert([
            ['id' => 1, 'shop_id' => 1, 'name' => 'wash, dry, fold'],
        ]);

        \DB::table('shop_services')->insert([
            ['id' => 1, 'shop_id' => 1, 'service_id' => 1, 'price' => 100.00],
        ]);

        \DB::table('orders')->insert([
            [
                'id' => 1,
                'customer_id' => 2,
                'shop_id' => 1,
                'shop_service_id' => 1,
                'service_mode' => 'both',
                'pickup_address' => '124 España Boulevard, Manila',
                'delivery_address' => '124 España Boulevard, Manila',
                'weight' => 4.50,
                'pickup_datetime' => now()->subDay(),
                'delivery_datetime' => now()->addDay(),
                'total_price' => 100.00,
                'status' => 'pending',
                'payment_method' => 'gcash',
                'payment_status' => 'unpaid',
                'created_at' => now()->subDays(2),
            ],
        ]);
    }
}
