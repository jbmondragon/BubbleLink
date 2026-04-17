<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SimulateLaundryApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-laundry-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate laundry app CRUD operations in the terminal.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Welcome to the Laundry App Simulation!');
        while (true) {
            $choice = $this->choice('Select an entity to manage', [
                'Users', 'Shops', 'Services', 'Shop Services', 'Orders', 'Exit'
            ]);
            if ($choice === 'Exit') {
                $this->info('Goodbye!');
                break;
            }
            switch ($choice) {
                case 'Users':
                    $this->manageUsers();
                    break;
                case 'Shops':
                    $this->manageShops();
                    break;
                case 'Services':
                    $this->manageServices();
                    break;
                case 'Shop Services':
                    $this->manageShopServices();
                    break;
                case 'Orders':
                    $this->manageOrders();
                    break;
            }
        }
    }

    protected function manageUsers()
    {
        $action = $this->choice('User actions', ['List', 'Create', 'Delete', 'Back']);
        if ($action === 'List') {
            $users = \App\Models\User::all();
            $this->table(['ID', 'Name', 'Email', 'Contact'], $users->map(fn($u) => [$u->id, $u->name, $u->email, $u->contact_number]));
        } elseif ($action === 'Create') {
            $name = $this->ask('Name');
            $email = $this->ask('Email');
            $password = $this->secret('Password');
            $contact = $this->ask('Contact Number (optional)');
            $user = \App\Models\User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'contact_number' => $contact,
            ]);
            $this->info('User created: ID ' . $user->id);
        } elseif ($action === 'Delete') {
            $id = $this->ask('User ID to delete');
            $user = \App\Models\User::find($id);
            if ($user) {
                $user->delete();
                $this->info('User deleted.');
            } else {
                $this->error('User not found.');
            }
        }
    }

    protected function manageShops()
    {
        $action = $this->choice('Shop actions', ['List', 'Create', 'Delete', 'Back']);
        if ($action === 'List') {
            $shops = \App\Models\Shop::all();
            $this->table(['ID', 'Manager', 'Name', 'Email', 'Address'], $shops->map(fn($s) => [$s->id, $s->user_id, $s->shop_name, $s->email, $s->address]));
        } elseif ($action === 'Create') {
            // Auto-create a manager user and use its id
            $manager = \App\Models\User::create([
                'name' => 'Manager_' . uniqid(),
                'email' => 'manager_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'contact_number' => null,
            ]);
            $user_id = $manager->id;
            $this->info('Created manager user with ID: ' . $user_id);
            $shop_name = $this->ask('Shop Name');
            $email = $this->ask('Email');
            $password = $this->secret('Password');
            $address = $this->ask('Address');
            $contact = $this->ask('Contact Number (optional)');
            $desc = $this->ask('Description (optional)');
            $shop = \App\Models\Shop::create([
                'user_id' => $user_id,
                'shop_name' => $shop_name,
                'email' => $email,
                'password' => bcrypt($password),
                'address' => $address,
                'contact_number' => $contact,
                'description' => $desc,
            ]);
            $this->info('Shop created: ID ' . $shop->id . ' (Manager User ID: ' . $user_id . ')');
        } elseif ($action === 'Delete') {
            $id = $this->ask('Shop ID to delete');
            $shop = \App\Models\Shop::find($id);
            if ($shop) {
                $shop->delete();
                $this->info('Shop deleted.');
            } else {
                $this->error('Shop not found.');
            }
        }
    }

    protected function manageServices()
    {
        $action = $this->choice('Service actions', ['List', 'Create', 'Delete', 'Back']);
        if ($action === 'List') {
            $services = \App\Models\Service::all();
            $this->table(['ID', 'Name'], $services->map(fn($s) => [$s->id, $s->name]));
        } elseif ($action === 'Create') {
            $name = $this->ask('Service Name');
            $service = \App\Models\Service::create(['name' => $name]);
            $this->info('Service created: ID ' . $service->id);
        } elseif ($action === 'Delete') {
            $id = $this->ask('Service ID to delete');
            $service = \App\Models\Service::find($id);
            if ($service) {
                $service->delete();
                $this->info('Service deleted.');
            } else {
                $this->error('Service not found.');
            }
        }
    }

    protected function manageShopServices()
    {
        $action = $this->choice('Shop Service actions', ['List', 'Create', 'Delete', 'Back']);
        if ($action === 'List') {
            $ss = \App\Models\ShopService::all();
            $this->table(['ID', 'Shop', 'Service', 'Price'], $ss->map(fn($s) => [$s->id, $s->shop_id, $s->service_id, $s->price]));
        } elseif ($action === 'Create') {
            $shop_id = $this->ask('Shop ID');
            $service_id = $this->ask('Service ID');
            $price = $this->ask('Price');
            $ss = \App\Models\ShopService::create([
                'shop_id' => $shop_id,
                'service_id' => $service_id,
                'price' => $price,
            ]);
            $this->info('Shop Service created: ID ' . $ss->id);
        } elseif ($action === 'Delete') {
            $id = $this->ask('Shop Service ID to delete');
            $ss = \App\Models\ShopService::find($id);
            if ($ss) {
                $ss->delete();
                $this->info('Shop Service deleted.');
            } else {
                $this->error('Shop Service not found.');
            }
        }
    }

    protected function manageOrders()
    {
        $action = $this->choice('Order actions', ['List', 'Create', 'Delete', 'Back']);
        if ($action === 'List') {
            $orders = \App\Models\Order::all();
            $this->table([
                'ID', 'Customer', 'Shop', 'Service', 'Mode', 'Pickup Addr', 'Delivery Addr', 'Pickup DT', 'Total', 'Status', 'Payment'
            ], $orders->map(fn($o) => [
                $o->id, $o->customer_id, $o->shop_id, $o->service_id, $o->service_mode, $o->pickup_address, $o->delivery_address, $o->pickup_datetime, $o->total_price, $o->status, $o->payment_status
            ]));
        } elseif ($action === 'Create') {
            $customer_id = $this->ask('Customer User ID');
            $shop_id = $this->ask('Shop ID');
            $service_id = $this->ask('Service ID');
            $service_mode = $this->choice('Service Mode', ['pickup only', 'delivery only', 'both']);
            $pickup_address = $this->ask('Pickup Address (optional)');
            $delivery_address = $this->ask('Delivery Address (optional)');
            $pickup_datetime = $this->ask('Pickup Datetime (YYYY-MM-DD HH:MM, optional)');
            $total_price = $this->ask('Total Price');
            $status = $this->choice('Status', ['pending', 'accepted', 'rejected', 'in-progress', 'completed']);
            $payment_status = $this->choice('Payment Status', ['paid', 'unpaid']);
            $order = \App\Models\Order::create([
                'customer_id' => $customer_id,
                'shop_id' => $shop_id,
                'service_id' => $service_id,
                'service_mode' => $service_mode,
                'pickup_address' => $pickup_address,
                'delivery_address' => $delivery_address,
                'pickup_datetime' => $pickup_datetime,
                'total_price' => $total_price,
                'status' => $status,
                'payment_status' => $payment_status,
            ]);
            $this->info('Order created: ID ' . $order->id);
        } elseif ($action === 'Delete') {
            $id = $this->ask('Order ID to delete');
            $order = \App\Models\Order::find($id);
            if ($order) {
                $order->delete();
                $this->info('Order deleted.');
            } else {
                $this->error('Order not found.');
            }
        }
    }
}
