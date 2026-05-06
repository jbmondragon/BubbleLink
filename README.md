# BubbleLink

## I. Project Description

**BubbleLink** is a web-based platform that connects customers with nearby laundry shops offering pickup and delivery services.

### Core Purpose

- Browse laundry shops
- Compare service prices
- Book pickup/delivery
- Track and manage orders

### User Types

- **Customer** → browse, order, view history
- **Shop Owner** → manage shops, handle orders
- **Admin** → approve/reject shop registrations

---

## II. Rationale

### Problem

- Manual workflows (walk-ins, calls, messages)
- Inefficient tracking and error-prone processes
- Hard to find services, compare prices, track orders

### Solution

- Centralized platform for booking and tracking
- Digital order management for shops
- Increased visibility for local businesses

### Impact (SDGs)

- **SDG 8** → Economic growth
- **SDG 9** → Innovation
- **SDG 11** → Better communities

---

# BubbleLink Cheat Sheet

## Overview

Laravel 12 platform with 3 portals:

- Customer → browse + order
- Shop Owner → manage shops + orders
- Platform Admin → approve owners

> Model: **Owner → Shop → Service → Order**

---

## Portals

- `/customer/login` (`/login`)
- `/shop-owner/login`
- `/platform-admin/login`

---

## Requirements

- PHP 8.2+, Composer
- Node + npm
- MySQL / MariaDB
- XAMPP

---

## Setup (Quick)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```
