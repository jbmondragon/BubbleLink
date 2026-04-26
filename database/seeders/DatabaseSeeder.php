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
            ['id' => 2, 'name' => 'Alice Manager', 'email' => 'alice@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000002', 'is_platform_admin' => false, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 3, 'name' => 'Bob Customer', 'email' => 'bob@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000003', 'is_platform_admin' => false, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 4, 'name' => 'Jane Owner', 'email' => 'jane@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000004', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 5, 'name' => 'Mark Staff', 'email' => 'mark@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000005', 'is_platform_admin' => false, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 6, 'name' => 'Mia Customer', 'email' => 'mia@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000006', 'is_platform_admin' => false, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 7, 'name' => 'System Admin', 'email' => 'admin@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000007', 'is_platform_admin' => true, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
        ]);

        \DB::table('organizations')->insert([
            ['id' => 1, 'name' => 'QuickClean Laundry', 'owner_user_id' => 1, 'created_at' => now()],
            ['id' => 2, 'name' => 'FreshFold Laundry', 'owner_user_id' => 4, 'created_at' => now()],
        ]);

        \DB::table('shops')->insert([
            ['id' => 1, 'organization_id' => 1, 'shop_name' => 'QuickClean Manila', 'address' => 'Manila City', 'contact_number' => '0287001001', 'description' => 'Pickup, delivery, and same-day folding for busy households.', 'created_at' => now()],
            ['id' => 2, 'organization_id' => 1, 'shop_name' => 'QuickClean Cebu', 'address' => 'Cebu City', 'contact_number' => '0327001002', 'description' => 'Branch focused on scheduled neighborhood pickups.', 'created_at' => now()],
            ['id' => 3, 'organization_id' => 2, 'shop_name' => 'FreshFold Davao', 'address' => 'Davao City', 'contact_number' => '0827001003', 'description' => 'Express steam and premium garment care for Davao customers.', 'created_at' => now()],
        ]);

        \DB::table('memberships')->insert([
            ['id' => 1, 'user_id' => 1, 'organization_id' => 1, 'shop_id' => null, 'role' => 'owner', 'created_at' => now()],
            ['id' => 2, 'user_id' => 2, 'organization_id' => 1, 'shop_id' => 1, 'role' => 'manager', 'created_at' => now()],
            ['id' => 3, 'user_id' => 4, 'organization_id' => 2, 'shop_id' => null, 'role' => 'owner', 'created_at' => now()],
            ['id' => 4, 'user_id' => 5, 'organization_id' => 2, 'shop_id' => 3, 'role' => 'staff', 'created_at' => now()],
            ['id' => 5, 'user_id' => 1, 'organization_id' => 2, 'shop_id' => 3, 'role' => 'manager', 'created_at' => now()],
        ]);

        \DB::table('services')->insert([
            ['id' => 1, 'organization_id' => 1, 'name' => 'wash, dry, fold'],
            ['id' => 2, 'organization_id' => 1, 'name' => 'dry cleaning'],
            ['id' => 3, 'organization_id' => 1, 'name' => 'ironing only'],
            ['id' => 4, 'organization_id' => 1, 'name' => 'full service'],
            ['id' => 5, 'organization_id' => 2, 'name' => 'wash, dry, fold'],
            ['id' => 6, 'organization_id' => 2, 'name' => 'express steam'],
        ]);

        \DB::table('shop_services')->insert([
            ['id' => 1, 'shop_id' => 1, 'service_id' => 1, 'price' => 100.00],
            ['id' => 2, 'shop_id' => 1, 'service_id' => 2, 'price' => 150.00],
            ['id' => 3, 'shop_id' => 2, 'service_id' => 1, 'price' => 110.00],
            ['id' => 4, 'shop_id' => 3, 'service_id' => 5, 'price' => 125.00],
            ['id' => 5, 'shop_id' => 3, 'service_id' => 6, 'price' => 180.00],
        ]);

        \DB::table('orders')->insert([
            [
                'id' => 1,
                'customer_id' => 3,
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
            [
                'id' => 2,
                'customer_id' => 6,
                'shop_id' => 3,
                'shop_service_id' => 4,
                'service_mode' => 'delivery_only',
                'pickup_address' => null,
                'delivery_address' => '45 Roxas Avenue, Davao City',
                'weight' => 3.00,
                'pickup_datetime' => null,
                'delivery_datetime' => now()->subDay(),
                'total_price' => 125.00,
                'status' => 'completed',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'created_at' => now()->subDays(4),
            ],
            [
                'id' => 3,
                'customer_id' => 3,
                'shop_id' => 2,
                'shop_service_id' => 3,
                'service_mode' => 'pickup_only',
                'pickup_address' => '88 Lahug, Cebu City',
                'delivery_address' => null,
                'weight' => 6.25,
                'pickup_datetime' => now()->addHours(6),
                'delivery_datetime' => null,
                'total_price' => 110.00,
                'status' => 'accepted',
                'payment_method' => 'gcash',
                'payment_status' => 'paid',
                'created_at' => now()->subHours(12),
            ],
        ]);
    }
}
