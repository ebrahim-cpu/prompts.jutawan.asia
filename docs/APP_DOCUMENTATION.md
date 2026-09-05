# PromptLib (prompts.jutawan.asia) — Complete Technical Documentation

> **Version:** 1.0.0 (Production)  
> **Framework:** Laravel 11.x | PHP 8.2+  
> **Domain:** [https://prompts.jutawan.asia](https://prompts.jutawan.asia)  
> **Server Environment:** cPanel / Apache / LiteSpeed with Pure-FTPd

---

## 1. System Architecture Overview

PromptLib is a curated AI prompt library and marketplace web application designed for prompt engineers, designers, content creators, and AI enthusiasts. It provides an intuitive platform to browse, search, copy, and manage prompts for AI models such as Midjourney, Stable Diffusion, DALL-E, and ChatGPT.

```
                  +--------------------------------------------------+
                  |                 Client Browser                   |
                  +-------------------------+------------------------+
                                            |
                                            v  HTTPS / Cloudflare
                  +--------------------------------------------------+
                  |           cPanel / LiteSpeed Web Server          |
                  |     (public_html/prompts.jutawan.asia)           |
                  +-------------------------+------------------------+
                                            | index.php entrypoint
                                            v
+-----------------------------------------------------------------------------------+
| Laravel 11 Core Application (/home/jutawnas/promtinglibabry)                     |
|                                                                                   |
|  +---------------------+  +----------------------+  +--------------------------+  |
|  |     Middleware      |  |     Controllers      |  |         Services         |  |
|  | - CheckSubscription |  | - HomeController     |  | - Laravel Socialite      |  |
|  | - LogVisitor        |  | - PromptController   |  | - Stripe Checkout SDK    |  |
|  | - AdminMiddleware   |  | - ReportController   |  | - Chart.js Data Feed     |  |
|  | - Authenticated     |  | - PricingController  |  | - Excel/PDF Exporters    |  |
|  +----------+----------+  +----------+-----------+  +------------+-------------+  |
|             |                        |                           |                |
|             +------------------------+---------------------------+                |
|                                      | Eloquent ORM                               |
|                                      v                                            |
|  +-----------------------------------------------------------------------------+  |
|  |                              Database Models                                |  |
|  |  User | Prompt | Category | Tag | UserAccessLog | VisitorLog                |  |
|  +-----------------------------------+-----------------------------------------+  |
+--------------------------------------|--------------------------------------------+
                                       v MySQL 8.0 / MariaDB
                  +--------------------------------------------------+
                  |                  MySQL Database                  |
                  +--------------------------------------------------+
```

---

## 2. Directory & Multi-Folder Deployment Architecture

On the production server, the application is split into two directories for security and cPanel compatibility:

1. **Web Document Root** (`/home/jutawnas/public_html/prompts.jutawan.asia/`):
   - Accessible to the public internet.
   - Contains `index.php`, `.htaccess`, compiled Vite frontend assets (`build/`), `favicon.ico`, and `robots.txt`.
   - Contains symlinks:
     - `storage` &rarr; `/home/jutawnas/promtinglibabry/storage/app/public`
     - `uploads` &rarr; `/home/jutawnas/promtinglibabry/public/uploads`
   - Contains server management scripts: `run_migrate.php`, `run_clear_view_cache.php`, `link_uploads.php`.

2. **Core Application Repository** (`/home/jutawnas/promtinglibabry/`):
   - Stored above the public web root (isolated from direct URL access).
   - Contains all business logic, models, controllers, blade views, configurations, vendor packages, artisan CLI, and database migrations.
   - Environment settings: `/home/jutawnas/promtinglibabry/.env`.
   - Dynamically rebinds `path.public` in `AppServiceProvider` to the cPanel web root:
     ```php
     $cpanelPublic = base_path('../public_html/prompts.jutawan.asia');
     if (is_dir($cpanelPublic)) {
         $this->app->bind('path.public', fn() => $cpanelPublic);
     }
     ```

---

## 3. Database Schema & Data Dictionary

### Table: `users`
Represents registered users, subscribers, and system administrators.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `name` | VARCHAR(255) | No | - | Full name |
| `email` | VARCHAR(255) | No | - | Unique email address |
| `email_verified_at` | TIMESTAMP | Yes | NULL | Email verification timestamp |
| `password` | VARCHAR(255) | Yes | NULL | Hashed password (nullable for Google OAuth) |
| `google_id` | VARCHAR(255) | Yes | NULL | Google OAuth Provider ID |
| `avatar` | VARCHAR(500) | Yes | NULL | Profile image URL or uploaded file path |
| `role` | VARCHAR(50) | No | `user` | User role (`user` or `admin`) |
| `tier` | VARCHAR(50) | No | `free` | Access tier (`free` or `premium`) |
| `subscription_starts_at` | TIMESTAMP | Yes | NULL | Start timestamp of current paid subscription |
| `premium_expires_at` | TIMESTAMP | Yes | NULL | Expiration timestamp (`NULL` = lifetime) |
| `stripe_customer_id` | VARCHAR(255) | Yes | NULL | Stripe Customer ID |
| `remember_token` | VARCHAR(100) | Yes | NULL | Remember me session token |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

### Table: `prompts`
Core repository of AI prompt templates.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `title` | VARCHAR(255) | No | - | Descriptive prompt title |
| `description` | TEXT | Yes | NULL | Prompt overview / use case explanation |
| `prompt_text` | LONGTEXT | No | - | Actual prompt text used with AI tools |
| `images` | JSON / LONGTEXT | Yes | NULL | Array of image URLs/paths illustrating output |
| `category` | VARCHAR(100) | Yes | `general` | Category slug (linked to `categories.slug`) |
| `rating` | INT | No | `5` | 1 to 5 star rating |
| `tags` | VARCHAR(500) | Yes | NULL | Comma-delimited list of tags |
| `is_premium` | BOOLEAN | No | `0` | `1` = Requires active Premium subscription |
| `is_featured` | BOOLEAN | No | `0` | `1` = Displayed prominently on homepage |
| `is_upcoming` | BOOLEAN | No | `0` | `1` = Coming soon preview item |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

### Table: `categories`
Dynamic prompt categories managed via Admin Panel.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `name` | VARCHAR(255) | No | - | Category title (e.g., "Portrait", "Anime") |
| `slug` | VARCHAR(255) | No | - | URL-friendly unique slug |
| `icon` | VARCHAR(50) | Yes | `🎨` | Emoji or icon identifier |
| `color` | VARCHAR(50) | Yes | `purple` | Tailwind color accent (e.g., `pink`, `blue`) |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

### Table: `tags`
Predefined and user-created prompt tags.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `name` | VARCHAR(255) | No | - | Clean tag label (without `#`) |
| `slug` | VARCHAR(255) | No | - | URL-friendly slug |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

### Table: `visitor_logs`
Anonymous traffic analytics for the homepage.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `ip_address` | VARCHAR(45) | No | - | Client IPv4 or IPv6 address |
| `user_agent` | VARCHAR(500) | Yes | NULL | Client HTTP User-Agent string |
| `url` | VARCHAR(500) | Yes | NULL | Target URL visited |
| `method` | VARCHAR(10) | Yes | `GET` | HTTP Request Method |
| `referer` | VARCHAR(500) | Yes | NULL | HTTP Referer URL |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Timestamp of visit |

### Table: `user_access_logs`
Security and audit trail of user sessions.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `user_id` | BIGINT UNSIGNED | Yes | NULL | ID of authenticated user |
| `user_name` | VARCHAR(255) | Yes | NULL | Snapshot of user name at time of event |
| `user_email` | VARCHAR(255) | Yes | NULL | Snapshot of user email |
| `event_type` | VARCHAR(50) | No | `LOGIN` | `LOGIN` or `LOGOUT` |
| `ip_address` | VARCHAR(45) | No | - | Client IP address |
| `user_agent` | VARCHAR(500) | Yes | NULL | Browser / Operating system user agent |
| `url` | VARCHAR(500) | Yes | NULL | Action URL |
| `method` | VARCHAR(10) | Yes | `POST` | HTTP Method |
| `referer` | VARCHAR(500) | Yes | NULL | Referer header |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Event timestamp |

---

## 4. User Roles, Tiers & Permissions

| Feature / Page | Guest | Free User | Premium User | Admin |
| :--- | :---: | :---: | :---: | :---: |
| Browse & Search Homepage | Yes | Yes | Yes | Yes |
| Filter by Category, Rating, Tags | Yes | Yes | Yes | Yes |
| View Free Prompt Details | Yes | Yes | Yes | Yes |
| Copy Free Prompt Text | Yes | Yes | Yes | Yes |
| View Premium Prompts (Preview) | Yes | Yes | Yes | Yes |
| Copy / Reveal Premium Prompt Text | No | No | Yes | Yes |
| View Self User Access Logs | No | Yes | Yes | Yes |
| Clear Self User Access Logs | No | Yes | Yes | Yes |
| Access Admin Panel (`/admin/*`) | No | No | No | Yes |
| Create, Edit, Delete Prompts | No | No | No | Yes |
| Manage Categories & Tags | No | No | No | Yes |
| Manage Users & Change Tiers | No | No | No | Yes |
| View Global Visitor & Access Logs | No | No | No | Yes |
| View Interactive Analytics Charts | No | No | No | Yes |
| Export Prompts & Reports (Excel/PDF) | No | No | No | Yes |

---

## 5. Subscription & Payment Engine (Stripe Checkout)

The application features a built-in Stripe Checkout gateway operating in Malaysian Ringgit (MYR).

### Available Plans
```php
PricingController::plans()
```
1. **1 Day** (`1day`): RM 3.00 (24-hour access)
2. **1 Week** (`1week`): RM 10.00 (7-day access)
3. **1 Month** (`1month`): RM 29.00 (30-day access) — *Most Popular*
4. **1 Year** (`1year`): RM 199.00 (365-day access)
5. **Lifetime** (`lifetime`): RM 499.00 (Indefinite / Permanent access)

### Payment Flow
1. User visits `/pricing` and selects a plan.
2. Form submits `POST /pricing/checkout` with `plan`.
3. `PricingController::checkout()` verifies Stripe credentials, initiates a `\Stripe\Checkout\Session`, sets client reference ID & metadata (`plan`, `user_id`), and redirects client to Stripe hosted checkout.
4. Upon successful payment, Stripe redirects to `/pricing/success?plan={plan}&session_id={CHECKOUT_SESSION_ID}`.
5. `PricingController::success()` verifies the session against the Stripe API:
   - Validates `payment_status === 'paid'`.
   - Matches authenticated user against session metadata.
   - Invokes `activatePremium()`, which updates `tier = 'premium'` and extends `premium_expires_at` by plan days (or sets `NULL` for lifetime).

### Automated Expiration Middleware
`\App\Http\Middleware\CheckSubscriptionExpiry::class`:
- Injected globally into the `web` middleware group in `bootstrap/app.php`.
- Executes on every web request.
- Calls `$user->autoExpireSubscription()`.
- If an active user's `premium_expires_at` has elapsed, their `tier` is automatically reset to `free`, safely revoking premium prompt access without needing cron workers.

---

## 6. Authentication & Social Login

### Google OAuth 2.0 Integration
- Handled via `\App\Http\Controllers\Auth\GoogleController`.
- Routes:
  - `GET /auth/google` &rarr; Redirects to Google Consent Screen.
  - `GET /auth/google/callback` &rarr; Handles Google return code.
- **cPanel/LiteSpeed Query String Patch**: LiteSpeed webservers often strip `$_SERVER['QUERY_STRING']` on URL rewrites. `GoogleController` actively parses `$_SERVER['REQUEST_URI']` and restores `code` into `$_GET` and `$request->query` to prevent callback authentication failures.
- **User Upsert**: If a user exists with matching `google_id` or `email`, their `google_id` and avatar are linked. Otherwise, a new user is automatically registered with `free` tier and verified email.

---

## 7. Reporting & Analytics System

Admin users have access to an interactive analytics suite at `/admin/reports`:
- **Chart.js Visualizations**:
  - **Visitors Trend**: Unique and total visitor counts.
  - **User Logins**: Session activity over time.
  - **Prompts Creation**: Catalog growth tracking.
- **Time Periods**: Daily (last 30 days), Weekly (last 12 weeks), or Monthly (12 months of selected year).
- **Asynchronous AJAX Updates**: Route `GET /admin/reports/data` feeds live JSON payloads to the frontend charts when dropdowns are changed without page reloads.
- **Data Export**:
  - `GET /admin/reports/export?format=excel`
  - `GET /admin/reports/export?format=pdf` (Print-friendly structured HTML/PDF format).

---

## 8. cPanel Deployment & Maintenance Guide

### Directory Permissions
```bash
chmod -R 755 /home/jutawnas/promtinglibabry/storage
chmod -R 755 /home/jutawnas/promtinglibabry/bootstrap/cache
chmod -R 755 /home/jutawnas/public_html/prompts.jutawan.asia
```

### Running Migrations on Shared Hosting
If SSH terminal is unavailable, visit:
`https://prompts.jutawan.asia/run_migrate.php`
This script executes `Artisan::call('migrate', ['--force' => true])` and prints output. *(Remember to delete or restrict access to this file in production).*

### Clearing Caches on Shared Hosting
Visit:
`https://prompts.jutawan.asia/run_clear_view_cache.php`
Executes view and config cache clearing directly via browser.

### Re-generating Storage Symlinks
Visit:
`https://prompts.jutawan.asia/link_uploads.php`
Creates symlinks between the public web folder and `/promtinglibabry/public/uploads` and `storage/app/public`.
