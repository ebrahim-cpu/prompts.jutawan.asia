# PromptLib (prompts.jutawan.asia)

An AI Prompt Library & Marketplace platform built with **Laravel 11**, **Tailwind CSS**, **Alpine.js**, and **Vite**.

- **Live URL:** [https://prompts.jutawan.asia](https://prompts.jutawan.asia)
- **Technical Manual:** [`docs/APP_DOCUMENTATION.md`](docs/APP_DOCUMENTATION.md)

---

## 1. System Architecture

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

## 2. Complete Database Structure & Data Dictionary

### Entity Relationship Overview

```
+----------------+          +-------------------+          +------------------+
|     users      | 1      * | user_access_logs  |          |   visitor_logs   |
|----------------|----------|-------------------|          |------------------|
| id (PK)        |          | id (PK)           |          | id (PK)          |
| role           |          | user_id (FK)      |          | ip_address       |
| tier           |          | event_type        |          | user_agent       |
| expires_at     |          | ip_address        |          | url, method      |
+----------------+          +-------------------+          +------------------+

+----------------+          +-------------------+          +------------------+
|   categories   | 1      * |      prompts      | *      * |       tags       |
|----------------|----------|-------------------|----------|------------------|
| id (PK)        |          | id (PK)           |          | id (PK)          |
| name           |          | category (slug)   |          | name             |
| slug (Unique)  |          | tags (csv list)   |          | slug (Unique)    |
| icon, color    |          | is_premium        |          +------------------+
+----------------+          | is_featured       |
                            | is_upcoming       |
                            +-------------------+
```

---

### Table: `users`
Represents registered users, subscribers, and system administrators.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `name` | VARCHAR(255) | No | - | Full name |
| `email` | VARCHAR(255) | No | - | Unique email address |
| `email_verified_at` | TIMESTAMP | Yes | NULL | Email verification timestamp |
| `password` | VARCHAR(255) | Yes | NULL | Bcrypt hashed password (nullable for Google OAuth) |
| `google_id` | VARCHAR(255) | Yes | NULL | Google OAuth Provider ID |
| `avatar` | VARCHAR(500) | Yes | NULL | Profile image URL or uploaded file path |
| `role` | VARCHAR(50) | No | `user` | User role (`user` or `admin`) |
| `tier` | VARCHAR(50) | No | `free` | Access tier (`free` or `premium`) |
| `subscription_starts_at` | TIMESTAMP | Yes | NULL | Start timestamp of current paid subscription |
| `premium_expires_at` | TIMESTAMP | Yes | NULL | Expiration timestamp (`NULL` = lifetime) |
| `stripe_customer_id` | VARCHAR(255) | Yes | NULL | Stripe Customer ID |
| `remember_token` | VARCHAR(100) | Yes | NULL | Remember me session token |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

---

### Table: `prompts`
Core repository of AI prompt templates.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :---: | :--- | :--- |
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

---

### Table: `categories`
Dynamic prompt categories managed via Admin Panel.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `name` | VARCHAR(255) | No | - | Category title (e.g., "Portrait", "Anime") |
| `slug` | VARCHAR(255) | No | - | URL-friendly unique slug |
| `icon` | VARCHAR(50) | Yes | `🎨` | Emoji or icon identifier |
| `color` | VARCHAR(50) | Yes | `purple` | Tailwind color accent (e.g., `pink`, `blue`) |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

---

### Table: `tags`
Predefined and user-created prompt tags.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `name` | VARCHAR(255) | No | - | Clean tag label (without `#`) |
| `slug` | VARCHAR(255) | No | - | URL-friendly slug |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Standard Laravel timestamps |

---

### Table: `visitor_logs`
Anonymous traffic analytics for the homepage.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `ip_address` | VARCHAR(45) | No | - | Client IPv4 or IPv6 address |
| `user_agent` | VARCHAR(500) | Yes | NULL | Client HTTP User-Agent string |
| `url` | VARCHAR(500) | Yes | NULL | Target URL visited |
| `method` | VARCHAR(10) | Yes | `GET` | HTTP Request Method |
| `referer` | VARCHAR(500) | Yes | NULL | HTTP Referer URL |
| `created_at`, `updated_at` | TIMESTAMP | Yes | NULL | Timestamp of visit |

---

### Table: `user_access_logs`
Security and audit trail of user sessions.
| Column | Type | Nullable | Default | Description |
| :--- | :--- | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO | Primary Key |
| `user_id` | BIGINT UNSIGNED | Yes | NULL | ID of authenticated user (foreign key to `users.id`) |
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

## 3. Features & Access Matrix

| Feature / Capability | Guest | Free User | Premium User | Admin |
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

## 4. Tech Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Database:** MySQL 8.0 / MariaDB
- **Frontend:** Blade Templates, Tailwind CSS 3.4, Alpine.js 3.x, Vite
- **Integrations:** Stripe Checkout SDK (MYR currency), Laravel Socialite (Google OAuth), Chart.js

---

## 5. Local Development Setup

### 1. Requirements
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ & NPM
- MySQL or SQLite

### 2. Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ebrahim-cpu/prompts.jutawan.asia.git
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
   Configure `.env` with database credentials, Stripe keys, and Google OAuth credentials.

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```
   Default seeded test credentials:
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
   Access application at: `http://127.0.0.1:8000`

---

## 6. Production Deployment (cPanel)

Detailed deployment instructions, symlink creation, directory mapping, and troubleshooting:  
👉 **[Read `docs/APP_DOCUMENTATION.md`](docs/APP_DOCUMENTATION.md)**

---

## License

Proprietary — All Rights Reserved.
