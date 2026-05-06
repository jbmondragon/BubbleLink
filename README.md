# BubbleLink

BubbleLink is a Laravel 12 laundry service platform with three live surfaces:
customer browsing and ordering, direct shop-owner operations, and platform-admin
approval of new shop owners.

## Portals

| Portal         | URL                     | Purpose                                                        |
| -------------- | ----------------------- | -------------------------------------------------------------- |
| Customer       | `/customer/login`       | Browse shops, place orders, and review order history           |
| Shop Owner     | `/shop-owner/login`     | Manage shops, fixed services, shop-service pricing, and orders |
| Platform Admin | `/platform-admin/login` | Review and approve or reject shop owner registrations          |

Each portal has its own entry route, but the app now uses a simpler runtime model:
shop owners act directly on their shops. The old organization, membership, guided
setup, and welcome-page flows are no longer part of the live app experience.

Default customer auth is also available at `/login` and `/register`.

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- MySQL or another Laravel-supported database

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

Windows PowerShell note:
use `npm.cmd run build` if PowerShell blocks `npm.ps1`.

## Seed Data

The database seeder currently creates one compact demo graph:

- shop owner: `john@example.com` / `password`
- customer: `bob@example.com` / `password`
- platform admin: `admin@bubblelink.test` / `password`
- one owner shop with one priced service and one sample order

## Live Functional Scope

### Customer

- browse public shop listings
- view shop details and available services
- place orders using pickup, delivery, both, or walk-in
- review order history and order details
- access order history from the top navigation while signed in

### Shop Owner

- create and edit shops
- work from a fixed per-shop service catalog
- assign and remove shop-service pricing
- review incoming orders
- create internal orders
- update order status, weight, and payment state

### Platform Admin

- review pending shop owner registrations
- approve or reject requests
- create an audit trail of approval decisions
- notify owners about approval outcomes

## Auth Flow

- customer registration and login are available through customer routes
- shop owner registration captures first-shop details during registration
- platform admin approval unlocks the owner workspace
- approved owners go directly to the business dashboard
- profile management remains active

## Verification Commands

```bash
php artisan test --compact
npm.cmd run build
```

Focused regression checks that match the current live flows:

```bash
php artisan test --compact --filter=CustomerOrderingTest
php artisan test --compact --filter=OwnerDashboardTest
php artisan test --compact --filter=OwnerAccountApprovalTest
```
