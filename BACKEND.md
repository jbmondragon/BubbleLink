# Back-End Structure

This document describes the current BubbleLink back-end after the simplification
work that removed the old organization-driven setup flow from the live app.

## Stack

| Tool           | Purpose                                                    |
| -------------- | ---------------------------------------------------------- |
| PHP 8.2        | Application runtime                                        |
| Laravel 12     | Routing, middleware, ORM, validation, auth                 |
| Eloquent ORM   | Model and relationship layer                               |
| Laravel Breeze | Login, registration, password reset, verification, profile |
| Pest           | Automated testing                                          |

## Current Domain Model

The live app is built around direct ownership and shop operations.

### Core tables

#### `users`

Holds customers, shop owners, and platform admins.

Important fields include:

- `is_platform_admin`
- `owner_registration_status`
- `approved_by_user_id`
- `owner_registration_reviewed_at`
- pending first-shop registration fields used during owner approval

#### `shops`

Each shop belongs directly to an owner through `owner_user_id` and stores:

- `shop_name`
- `address`
- `contact_number`
- `description`

#### `services`

Service records belong to a specific shop through `shop_id`. The live owner flow
auto-provisions a fixed default service set per shop instead of letting owners
create arbitrary service types from the UI.

#### `shop_services`

Priced sellable entries that connect a shop to one of its service records and
store the live selling price used for ordering.

#### `orders`

Customer and owner-created orders. Key fields include:

- `customer_id`
- `shop_id`
- `shop_service_id`
- `service_mode`
- pickup and delivery address / schedule fields
- `weight`
- `total_price`
- `status`
- `payment_method`
- `payment_status`
- rating fields retained for compatibility, but not exposed in the current customer UI

#### `owner_registration_reviews`

Audit log for platform-admin approval and rejection decisions.

### Legacy tables

`organizations` and `memberships` still exist in the codebase and schema as legacy
surfaces, but they are no longer the primary live runtime model. New work should
follow the direct owner → shop → service → order flow unless those legacy features
are explicitly being revived.

## Main Models

### `User`

- customer orders
- shop ownership
- platform-admin approval metadata
- helper methods for owner approval state

### `Shop`

- belongs to an owner
- has many `shopServices`
- has many `orders`

### `Service`

- belongs to a shop
- has many `shopServices`
- auto-provisions the fixed default service catalog per shop

### `ShopService`

- belongs to a shop
- belongs to a service
- stores the price used for ordering
- has many `orders`

### `Order`

- belongs to a customer
- belongs to a shop
- belongs to a shop service

### `OwnerRegistrationReview`

- belongs to a shop owner
- belongs to a platform admin

## Route Surfaces

### Public

- `/`
- `/shops`
- `/shops/{shop}/details`

Handled by `CustomerShopController`.

### Customer

- place orders
- view order history
- view order details
- submit ratings through a retained endpoint

The customer rating endpoint still exists in code, but the current live UI no
longer renders customer rating controls.

Handled by `CustomerOrderController`.

### Business / Shop Owner

- dashboard
- shops create, edit, show
- services and pricing
- order management
- shop service assignment

Handled by `DashboardController`, `ShopController`, `ServiceController`,
`ShopServiceController`, and `OrderController`.

### Platform Admin

- owner registration review queue
- approve / reject owner registrations

Handled by `PlatformAdminOwnerApprovalController`.

### Auth and Profile

- customer, shop-owner, and platform-admin login entry points
- customer and shop-owner registration entry points
- password reset, email verification, profile update

Handled by Breeze controllers in `app/Http/Controllers/Auth` plus `ProfileController`.

Current guest auth entry points include:

- `/login` and `/register` for the default customer flow
- `/customer/login` and `/customer/register`
- `/shop-owner/login` and `/shop-owner/register`
- `/platform-admin/login`

## Middleware and Access

The main route guard is `EnsureAreaAccess`, registered as the `area` middleware
alias in `bootstrap/app.php`.

Important areas in the live app:

- `customer`
- `platform-admin`
- `business`
- `dashboard`

This middleware decides whether a signed-in user should be treated as a customer,
platform admin, or business user based on their account state.

## Current Controller Responsibilities

### `CustomerShopController`

- prepares public shop listing cards
- prepares shop detail data for rendering
- keeps view logic out of Blade

### `CustomerOrderController`

- builds customer order summaries
- handles customer order creation
- shows customer order details
- retains a legacy rating endpoint that is no longer surfaced in the live UI

### `DashboardController`

- redirects platform admins to the approval queue
- prepares owner dashboard summary metrics

### `ShopController`

- create and update shop records
- show shop workspace details and recent activity

### `ServiceController`

- ensures each owner shop has the fixed default service set
- present service and pricing management data

### `OrderController`

- filter and list business orders
- create internal orders
- update order status, weight, and payment state

### `PlatformAdminOwnerApprovalController`

- list pending and reviewed owner registrations
- approve or reject owner registrations
- record review history
- send approval or rejection notifications

## Policies

Policies are still registered in `AppServiceProvider` and remain the main write-side
authorization mechanism for business actions.

Active policies include:

- `ShopPolicy`
- `OrderPolicy`
- `ShopServicePolicy`

## Notifications

Two notifications support owner registration review outcomes:

- `ShopOwnerRegistrationApprovedNotification`
- `ShopOwnerRegistrationRejectedNotification`

Approval now unlocks the owner dashboard flow directly rather than sending the user
into a guided organization setup wizard.

## Testing

Key verification commands:

```bash
php artisan test --compact
php artisan test --compact --filter=CustomerOrderingTest
php artisan test --compact --filter=OwnerDashboardTest
php artisan test --compact --filter=OwnerAccountApprovalTest
```

The test suite is the preferred regression check for backend behavior changes.

- `tests/Feature/CustomerOrderingTest.php` — public browsing, search, ordering, and order-history flow
- `tests/Feature/OwnerDashboardTest.php` — owner dashboard, services, internal orders, and shop access rules
- `tests/Feature/OwnerAccountApprovalTest.php` — platform-admin approval, rejection, audit log, and notifications
- `tests/Feature/RouteAreaAccessTest.php` — area middleware and cross-portal access boundaries
