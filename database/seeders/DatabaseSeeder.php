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
            ['id' => 1, 'name' => 'Sofia\'s Bubble Owner', 'email' => 'sofia@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000001', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 2, 'name' => 'Bob Customer', 'email' => 'bob@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000002', 'is_platform_admin' => false, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 3, 'name' => 'System Admin', 'email' => 'admin@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000003', 'is_platform_admin' => true, 'owner_registration_status' => null, 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => null, 'created_at' => now()],
            ['id' => 4, 'name' => 'QuickClean Owner', 'email' => 'quickclean@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000004', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 5, 'name' => 'EcoWash Owner', 'email' => 'ecowash@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000005', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 6, 'name' => 'Sparkle Owner', 'email' => 'sparkle@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000006', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 7, 'name' => '24/7 Hub Owner', 'email' => '247hub@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000007', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 8, 'name' => 'Fresh Clean Owner', 'email' => 'freshclean@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000008', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 9, 'name' => 'ProWash Owner', 'email' => 'prowash@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000009', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 10, 'name' => 'Gentle Touch Owner', 'email' => 'gentle@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000010', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 11, 'name' => 'Budget Wash Owner', 'email' => 'budget@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000011', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
            ['id' => 12, 'name' => 'Royal Cleaners Owner', 'email' => 'royal@bubblelink.test', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'remember_token' => Str::random(10), 'contact_number' => '09170000012', 'is_platform_admin' => false, 'owner_registration_status' => 'approved', 'approved_by_user_id' => null, 'owner_registration_reviewed_at' => now(), 'created_at' => now()],
        ]);

        \DB::table('shops')->insert([
            ['id' => 1, 'owner_user_id' => 1, 'shop_name' => 'Sofia\'s Bubble', 'address' => 'Santo Nino St. Tacloban City', 'contact_number' => '0287001001', 'description' => 'Pickup, delivery, and same-day folding for busy households.', 'created_at' => now()],
            ['id' => 2, 'owner_user_id' => 4, 'shop_name' => 'QuickClean Laundry', 'address' => 'Real St. Tacloban City', 'contact_number' => '0287001002', 'description' => 'Express laundry services with 2-hour turnaround time.', 'created_at' => now()],
            ['id' => 3, 'owner_user_id' => 5, 'shop_name' => 'EcoWash Center', 'address' => 'Justice Romualdez St. Tacloban City', 'contact_number' => '0287001003', 'description' => 'Eco-friendly laundry with organic detergents and gentle care.', 'created_at' => now()],
            ['id' => 4, 'owner_user_id' => 6, 'shop_name' => 'Sparkle Wash', 'address' => 'Magsaysay Blvd. Tacloban City', 'contact_number' => '0287001004', 'description' => 'Premium wash and fold service with fabric softener and fragrance options.', 'created_at' => now()],
            ['id' => 5, 'owner_user_id' => 7, 'shop_name' => '24/7 Laundry Hub', 'address' => 'Kanligue St. Tacloban City', 'contact_number' => '0287001005', 'description' => 'Round-the-clock laundry service perfect for shift workers and busy professionals.', 'created_at' => now()],
            ['id' => 6, 'owner_user_id' => 8, 'shop_name' => 'Fresh & Clean', 'address' => 'Paterno St. Tacloban City', 'contact_number' => '0287001006', 'description' => 'Family-oriented laundry service with special care for children\'s clothing.', 'created_at' => now()],
            ['id' => 7, 'owner_user_id' => 9, 'shop_name' => 'ProWash Services', 'address' => 'Capitol Site, Tacloban City', 'contact_number' => '0287001007', 'description' => 'Commercial-grade laundry for businesses and bulk orders.', 'created_at' => now()],
            ['id' => 8, 'owner_user_id' => 10, 'shop_name' => 'Gentle Touch Laundry', 'address' => 'T. Claudio St. Tacloban City', 'contact_number' => '0287001008', 'description' => 'Specialized care for delicate fabrics, silk, and expensive clothing.', 'created_at' => now()],
            ['id' => 9, 'owner_user_id' => 11, 'shop_name' => 'Budget Wash', 'address' => 'Abucay St. Tacloban City', 'contact_number' => '0287001009', 'description' => 'Affordable laundry solutions for students and budget-conscious customers.', 'created_at' => now()],
            ['id' => 10, 'owner_user_id' => 12, 'shop_name' => 'Royal Cleaners', 'address' => 'Cruz St. Tacloban City', 'contact_number' => '0287001010', 'description' => 'Luxury laundry service with premium detergents and white-glove handling.', 'created_at' => now()],
        ]);

        \DB::table('services')->insert([
            ['id' => 1, 'name' => 'wash, dry, fold'],
            ['id' => 2, 'name' => 'dry cleaning'],
            ['id' => 3, 'name' => 'express wash'],
            ['id' => 4, 'name' => 'heavy duty wash'],
            ['id' => 5, 'name' => 'delicate care'],
        ]);

        \DB::table('shop_services')->insert([
            // Sofia's Bubble - Standard household services
            ['id' => 1, 'shop_id' => 1, 'service_id' => 1, 'max_weight_kg' => 8.00, 'price' => 100.00],
            ['id' => 2, 'shop_id' => 1, 'service_id' => 2, 'max_weight_kg' => 5.00, 'price' => 250.00],
            ['id' => 3, 'shop_id' => 1, 'service_id' => 5, 'max_weight_kg' => 6.00, 'price' => 150.00],
            
            // QuickClean Laundry - Express focused
            ['id' => 4, 'shop_id' => 2, 'service_id' => 1, 'max_weight_kg' => 10.00, 'price' => 120.00],
            ['id' => 5, 'shop_id' => 2, 'service_id' => 3, 'max_weight_kg' => 7.00, 'price' => 200.00],
            ['id' => 6, 'shop_id' => 2, 'service_id' => 4, 'max_weight_kg' => 15.00, 'price' => 180.00],
            
            // EcoWash Center - Eco-friendly and delicate
            ['id' => 7, 'shop_id' => 3, 'service_id' => 1, 'max_weight_kg' => 8.00, 'price' => 110.00],
            ['id' => 8, 'shop_id' => 3, 'service_id' => 2, 'max_weight_kg' => 5.00, 'price' => 280.00],
            ['id' => 9, 'shop_id' => 3, 'service_id' => 5, 'max_weight_kg' => 6.00, 'price' => 170.00],
            
            // Sparkle Wash - Premium services
            ['id' => 10, 'shop_id' => 4, 'service_id' => 1, 'max_weight_kg' => 9.00, 'price' => 130.00],
            ['id' => 11, 'shop_id' => 4, 'service_id' => 2, 'max_weight_kg' => 6.00, 'price' => 290.00],
            ['id' => 12, 'shop_id' => 4, 'service_id' => 5, 'max_weight_kg' => 7.00, 'price' => 180.00],
            
            // 24/7 Laundry Hub - All services available
            ['id' => 13, 'shop_id' => 5, 'service_id' => 1, 'max_weight_kg' => 12.00, 'price' => 140.00],
            ['id' => 14, 'shop_id' => 5, 'service_id' => 2, 'max_weight_kg' => 5.00, 'price' => 300.00],
            ['id' => 15, 'shop_id' => 5, 'service_id' => 3, 'max_weight_kg' => 8.00, 'price' => 220.00],
            ['id' => 16, 'shop_id' => 5, 'service_id' => 4, 'max_weight_kg' => 20.00, 'price' => 200.00],
            ['id' => 17, 'shop_id' => 5, 'service_id' => 5, 'max_weight_kg' => 6.00, 'price' => 190.00],
            
            // Fresh & Clean - Family focused
            ['id' => 18, 'shop_id' => 6, 'service_id' => 1, 'max_weight_kg' => 15.00, 'price' => 90.00],
            ['id' => 19, 'shop_id' => 6, 'service_id' => 4, 'max_weight_kg' => 18.00, 'price' => 160.00],
            ['id' => 20, 'shop_id' => 6, 'service_id' => 5, 'max_weight_kg' => 10.00, 'price' => 140.00],
            
            // ProWash Services - Commercial heavy duty
            ['id' => 21, 'shop_id' => 7, 'service_id' => 1, 'max_weight_kg' => 25.00, 'price' => 150.00],
            ['id' => 22, 'shop_id' => 7, 'service_id' => 2, 'max_weight_kg' => 10.00, 'price' => 320.00],
            ['id' => 23, 'shop_id' => 7, 'service_id' => 3, 'max_weight_kg' => 20.00, 'price' => 250.00],
            ['id' => 24, 'shop_id' => 7, 'service_id' => 4, 'max_weight_kg' => 30.00, 'price' => 220.00],
            
            // Gentle Touch Laundry - Delicate focused
            ['id' => 25, 'shop_id' => 8, 'service_id' => 2, 'max_weight_kg' => 4.00, 'price' => 350.00],
            ['id' => 26, 'shop_id' => 8, 'service_id' => 5, 'max_weight_kg' => 5.00, 'price' => 200.00],
            
            // Budget Wash - Basic affordable
            ['id' => 27, 'shop_id' => 9, 'service_id' => 1, 'max_weight_kg' => 10.00, 'price' => 70.00],
            ['id' => 28, 'shop_id' => 9, 'service_id' => 3, 'max_weight_kg' => 8.00, 'price' => 150.00],
            
            // Royal Cleaners - Luxury premium
            ['id' => 29, 'shop_id' => 10, 'service_id' => 1, 'max_weight_kg' => 8.00, 'price' => 200.00],
            ['id' => 30, 'shop_id' => 10, 'service_id' => 2, 'max_weight_kg' => 5.00, 'price' => 450.00],
            ['id' => 31, 'shop_id' => 10, 'service_id' => 3, 'max_weight_kg' => 6.00, 'price' => 300.00],
            ['id' => 32, 'shop_id' => 10, 'service_id' => 5, 'max_weight_kg' => 6.00, 'price' => 250.00],
        ]);

        \DB::table('orders')->insert([
            [
                'id' => 1,
                'customer_id' => 2,
                'shop_id' => 1,
                'shop_service_id' => 1,
                'service_mode' => 'both',
                'pickup_address' => 'University of the Philippines Tacloban, Tacloban City',
                'delivery_address' => 'University of the Philippines Tacloban, Tacloban City',
                'number_of_loads' => 1,
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
