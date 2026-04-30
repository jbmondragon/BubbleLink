# BubbleLink

BubbleLink is a multi-portal laundry service platform built with Laravel 12. It connects customers with laundry shops and gives shop owners tools to manage their business.

---

## How It Works

The platform has three separate user portals:

| Portal         | URL                     | Purpose                                                 |
| -------------- | ----------------------- | ------------------------------------------------------- |
| Customer       | `/customer/login`       | Browse shops, place and track orders                    |
| Shop Owner     | `/shop-owner/login`     | Manage organization, shops, services, staff, and orders |
| Platform Admin | `/platform-admin/login` | Review and approve shop owner registrations             |

Each portal is isolated — accounts can only log in through their designated portal.

---

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL (or any Laravel-supported database)

---

## Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Configure your database in .env
# DB_DATABASE=bubblelink
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Seed sample data
php artisan db:seed

# 7. Install and build frontend assets
npm install
npm run build

# 8. Start the development server
php artisan serve
```

> **Windows / PowerShell users:** Run `Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass` before running npm commands if you encounter a script execution error.

---

## Seeded Test Accounts

All accounts use the password: **`password`**

| Name          | Email                 | Role                             |
| ------------- | --------------------- | -------------------------------- |
| System Admin  | admin@bubblelink.test | Platform Admin                   |
| John Doe      | john@example.com      | Shop Owner (QuickClean Laundry)  |
| Jane Owner    | jane@example.com      | Shop Owner (FreshFold Laundry)   |
| Alice Manager | alice@example.com     | Shop Manager (QuickClean Manila) |
| Mark Staff    | mark@example.com      | Staff (FreshFold Davao)          |
| Bob Customer  | bob@example.com       | Customer                         |
| Mia Customer  | mia@example.com       | Customer                         |

---

## Functionalities

### Customer Portal (`/customer/login`)

- Browse available laundry shops
- View shop details and offered services
- Place orders with service modes: pickup & delivery, pickup only, delivery only, or walk-in
- Track order status and history
- Rate completed orders

### Shop Owner Portal (`/shop-owner/login`)

> Requires an approved shop owner account. New registrations are reviewed by the Platform Admin before access is granted.

- **Setup Wizard** — Create your organization, add shops, define services, and invite staff on first login
- **Organization Management** — Switch between organizations if you belong to multiple
- **Shop Management** — Create, edit, and delete shops
- **Service Management** — Define services scoped to your organization
- **Shop Services** — Assign services to specific shops with custom pricing
- **Staff & Memberships** — Add or remove staff members and assign them to shops
- **Order Management** — View incoming orders and update their status

### Platform Admin Portal (`/platform-admin/login`)

- View all pending shop owner registration requests
- Approve or reject registrations
- Approved owners are notified and can proceed to set up their organization

---

## Running Tests

```bash
php artisan test --compact
```

---

## Code Style

After modifying PHP files, run Pint to enforce consistent formatting:

```bash
vendor/bin/pint --dirty
```
