# BubbleLink

_This project is developed by **Jake B. Mondragon**, **Louis Kent S. Dela Cruz**, and **Harry Gomez**_

## I. Project Description

**BubbleLink** is a web-based platform that aims to connect customers and nearby laundry shops that offer pickup and delivery services. The proposed system aims to enable customers to browse existing laundry shops and compare service prices, and schedule a pickup or delivery directly through online booking.

### User Types

- **Customer** → browse, order, view history
- **Shop Owner** → manage shops, handle orders
- **Admin** → approve/reject shop registrations

---

## II. Rationale

Laundry shops in Tacloban still operate in traditional methods such as walk-in customers, phone messages and calls for service requests. These methods often lead to inefficient order management since existing methods are prone to error or mismanagement due to high effort needed for checking and manually tracking all of the laundry requests by different users. Customers also face difficulty identifying which laundromats offer pickup services, comparing prices, or tracking the progress of their laundry orders. This lack of a centralized system creates inconvenience for both customers and laundromat operators.
The proposed system addresses these challenges by providing a structured platform where customers can easily find laundromats, schedule pickups, and track orders. For laundromat owners, the system improves operational efficiency by organizing incoming service requests and allowing them to manage orders digitally.

### Impact (SDGs)

Additionally, the system supports small local businesses by providing laundromats with a digital presence and expanding their customer reach. It aligns with the following Sustainable Development Goals:

- **SDG 8** → Decent Work and Economic Growth by helping small businesses improve productivity and generate more income opportunities.
- **SDG 9** → Industry, Innovation and Infrastructure through the adoption of digital solutions that modernize traditional laundry service operations.
- **SDG 11** → Sustainable Cities and Communities by fostering more connected, efficient, and service-oriented local communities.

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
