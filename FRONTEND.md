# Front-End Structure

## Stack

| Tool            | Purpose                                                                    |
| --------------- | -------------------------------------------------------------------------- |
| Vite            | Build pipeline for CSS and JavaScript                                      |
| Tailwind CSS v4 | Utility layer used alongside shared semantic classes                       |
| Alpine.js       | Small interactive behaviors for forms, navigation, modals, and stateful UI |
| Axios           | HTTP bootstrap with CSRF-friendly defaults                                 |
| Blade           | Server-rendered HTML templates                                             |

## Current Resource Structure

```text
resources/
├── css/
│   ├── app.css
│   └── base/
│       ├── global.css
│       ├── layout.css
│       └── bubbles.css
├── js/
│   ├── app.js
│   ├── bootstrap.js
│   └── alpine-components.js
└── views/
    ├── auth/
    ├── components/
    ├── customer/
    ├── layouts/
    ├── orders/
    ├── platform-admin/
    ├── profile/
    ├── services/
    └── shops/
```

## CSS Organization

### `resources/css/app.css`

This is the single CSS entry point loaded through Vite. It imports only the live,
shared CSS layers:

- `base/global.css`
- `base/layout.css`
- `base/bubbles.css`

`resources/css/setup.css` exists in the repo, but it is not part of the current
Vite entry chain.

### `resources/css/base/global.css`

Contains base page-level styling such as:

- body sizing
- overflow behavior
- overall background treatment

### `resources/css/base/layout.css`

This is the main semantic component layer. It now contains shared classes for the
live rendered surfaces instead of leaving long presentation-heavy Tailwind strings
inside Blade templates.

Important groups include:

- app and guest shell classes
- customer page classes
- owner page classes
- admin page classes
- profile page classes
- auth page classes

The goal is:

- Blade should stay closer to structure and meaning
- repeated styling should live in CSS
- only rendered pages should receive new shared classes

### `resources/css/base/bubbles.css`

Contains the decorative floating background animation and its responsive / reduced
motion behavior.

## Blade Structure

### Layouts

`resources/views/layouts/app.blade.php`

- authenticated shell
- navigation
- shared Vite assets

`resources/views/layouts/guest.blade.php`

- guest auth shell
- centered guest card layout

### Rendered View Groups

#### `resources/views/auth/`

Live Breeze auth templates for:

- login
- register
- forgot password
- reset password
- confirm password
- verify email

These now use shared `auth-*` CSS classes for headings, notes, link rows, banners,
panels, actions, and textarea styling.

The shared templates are reused for customer, shop-owner, and platform-admin
login screens by passing different headings, descriptions, and form actions from
the auth controllers.

#### `resources/views/customer/`

Customer-facing shop browsing and order pages. These now use shared `customer-*`
classes instead of large repeated utility strings.

Current customer UI highlights:

- the shop catalog supports searching by shop name or service name
- signed-in customers get an order-history shortcut in the top navigation
- the shop detail page is simplified to compact shop info plus a service table
- customer order detail pages are reduced to core order summary information
- guest catalog pages surface portal login shortcuts
- customer-facing rating UI has been removed from rendered pages

#### `resources/views/shops/`, `resources/views/services/`, `resources/views/orders/`, `resources/views/dashboard.blade.php`

Owner-facing business pages. These now use shared `owner-*` classes.

#### `resources/views/platform-admin/`

Platform admin owner-approval queue. This now uses shared `admin-*` classes.

#### `resources/views/profile/`

Profile page and its Breeze partials. These now use shared `profile-*` classes.

## JavaScript Organization

### `resources/js/bootstrap.js`

Sets up Axios and shared request defaults.

### `resources/js/app.js`

Main JavaScript entry point. It loads Bootstrap, Alpine, and the shared Alpine and
DOM helpers.

### `resources/js/alpine-components.js`

Contains the extracted interactive logic that used to be mixed into Blade.

Current responsibilities include:

- navigation menu state
- dropdown behavior
- modal helpers
- owner order form behavior
- customer order form behavior
- flash message behavior
- confirm-submit DOM handlers

This file is now the main place for reusable front-end interaction logic.

It does not currently power the simplified customer catalog and order-history
shortcut, which are server-rendered directly in Blade.

## Rendering Philosophy

The current front-end follows these rules:

- JavaScript logic belongs in JavaScript files, not inline Blade object literals when it can be shared.
- Repeated presentation belongs in shared CSS classes, not repeated long utility strings.
- Controllers prepare data for views instead of Blade deriving it.
- Dead pages and assets should be deleted, not refactored.
- Only still-rendered surfaces should be cleaned up.

## Build Commands

```bash
npm run dev
npm run build
```

On Windows PowerShell, use:

```bash
npm.cmd run build
```

## Validation Commands

```bash
npm.cmd run build
php artisan test --compact
```

Focused front-end smoke checks:

```bash
php artisan test --compact --filter=CustomerOrderingTest
php artisan test --compact --filter=OwnerDashboardTest
```
