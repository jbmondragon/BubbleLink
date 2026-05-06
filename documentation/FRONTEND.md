---

## CSS
- **app.css** → main entry (imports base files)
- **global.css** → base styles (body, background)
- **layout.css** → shared semantic classes  
  (`auth-*`, `customer-*`, `owner-*`, `admin-*`, `profile-*`)
- **bubbles.css** → background animation

Rule:
- Reusable styles → CSS
- Avoid long Tailwind strings in Blade

---

## Blade

### Layouts

- `layouts/app` → authenticated shell
- `layouts/guest` → auth pages

### Views

- **auth/** → Breeze templates (shared, role-based)
- **customer/** → shop browsing + orders
- **shops/services/orders/dashboard** → owner UI
- **platform-admin/** → approval queue
- **profile/** → user profile

---

## JavaScript

- **bootstrap.js** → Axios setup
- **app.js** → main entry (loads Alpine)
- **alpine-components.js** → reusable UI logic:
    - nav, dropdowns, modals
    - order forms (customer + owner)
    - flash messages
    - confirm actions

---

## Front-End Rules

- JS logic → JS files (not Blade)
- Repeated UI → CSS classes
- Controllers prepare data (not Blade)
- Only improve live pages

---

## Build

```bash
npm run dev
npm run build
# Windows:
npm.cmd run build
```
