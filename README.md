# PromptLib (prompts.jutawan.asia)

An AI Prompt Library & Marketplace platform built with **Laravel 11**, **Tailwind CSS**, **Alpine.js**, and **Vite**.

Live Site: [https://prompts.jutawan.asia](https://prompts.jutawan.asia)  
Full Documentation: [`docs/APP_DOCUMENTATION.md`](docs/APP_DOCUMENTATION.md)

---

## Features

- **Prompt Catalog & Discovery:** Explore AI prompts for Midjourney, Stable Diffusion, DALL-E, and ChatGPT with category, tag, and rating filters.
- **Tiered Access & Subscriptions:** Free tier and Premium tier with Stripe Checkout integration (MYR currency: Daily, Weekly, Monthly, Yearly, Lifetime).
- **Automated Subscription Expiration:** Global middleware auto-downgrades expired memberships back to the free tier seamlessly.
- **Google OAuth & Password Authentication:** Powered by Laravel Breeze and Socialite with LiteSpeed query string normalization.
- **Admin Management Suite:**
  - Full CRUD for Prompts, Categories, Tags, and Users.
  - Multi-image uploads with lightbox preview.
  - Excel and PDF export for prompts catalog.
  - Visitor logging and user access (login/logout) audit tracking.
  - Interactive Chart.js analytics dashboard (Daily, Weekly, Monthly metrics).
- **Optimized for Shared Hosting / cPanel:** Built-in public path re-binding, LiteSpeed query fix, and web-based helper scripts.

---

## Tech Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Database:** MySQL / MariaDB
- **Frontend:** Blade Templates, Tailwind CSS 3.4, Alpine.js 3.x, Vite
- **Integrations:** Stripe Checkout SDK, Laravel Socialite (Google OAuth), Chart.js

---

## Local Development Setup

### 1. Requirements
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ & NPM
- MySQL or SQLite

### 2. Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd prompts.jutawan.asia
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), Stripe credentials, and Google OAuth credentials.

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```
   Default seeded users:
   - **Admin:** `admin@example.com` / `password`
   - **Premium:** `premium@example.com` / `password`
   - **Free:** `free@example.com` / `password`

5. **Link Storage:**
   ```bash
   php artisan storage:link
   ```

6. **Compile Assets & Start Dev Server:**
   ```bash
   npm run dev
   php artisan serve
   ```
   Visit: `http://127.0.0.1:8000`

---

## Production Deployment (cPanel)

For production deployment details, symlink architecture, directory mapping, and troubleshooting, see the master documentation:
👉 **[Read `docs/APP_DOCUMENTATION.md`](docs/APP_DOCUMENTATION.md)**

---

## License

Proprietary — All Rights Reserved.
