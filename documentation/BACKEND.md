# ⚡ BubbleLink Back-End Cheat Sheet

## Stack

- PHP 8.2 + Laravel 12
- Eloquent ORM
- Laravel Breeze (auth)
- Pest (testing)

---

## Core Model (Live Flow)

**User → Shop → Service → ShopService → Order**

- Users: customers, owners, admins
- Shops: owned by user
- Services: fixed per shop (auto-created)
- ShopServices: priced services
- Orders: customer purchases

---

## Key Tables

- `users` → roles + owner approval state
- `shops` → owned by user
- `services` → base service types
- `shop_services` → pricing layer
- `orders` → transactions
- `owner_registration_reviews` → admin audit log

---

## Main Models

- **User** → roles, ownership, approvals
- **Shop** → has services + orders
- **Service** → predefined per shop
- **ShopService** → price + sellable unit
- **Order** → customer purchase
- **OwnerRegistrationReview** → admin decisions

---

## Routes

### Public

- `/`, `/shops`, `/shops/{shop}/details`

### Customer

- order creation, history, details
- (ratings exist but hidden in UI)

### Business (Owner)

- dashboard, shops, services, orders

### Platform Admin

- approve/reject shop owners

### Auth

- `/login`, `/register`
- `/customer/*`, `/shop-owner/*`, `/platform-admin/login`

---

## Middleware

- `area` (EnsureAreaAccess)
    - `customer`
    - `business`
    - `platform-admin`
    - `dashboard`

---

## Controllers

- **CustomerShopController** → shop listing/details
- **CustomerOrderController** → customer orders
- **DashboardController** → owner/admin routing
- **ShopController** → manage shops
- **ServiceController** → default services
- **ShopServiceController** → pricing layer
- **OrderController** → manage orders
- **PlatformAdminOwnerApprovalController** → approvals

---

## Policies

- ShopPolicy
- OrderPolicy
- ShopServicePolicy

---

## Notifications

- Owner approved
- Owner rejected

---

## Testing

```bash
php artisan test --compact
php artisan test --filter=CustomerOrderingTest
php artisan test --filter=OwnerDashboardTest
php artisan test --filter=OwnerAccountApprovalTest
```
