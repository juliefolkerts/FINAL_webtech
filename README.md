# Bloom Flower Shop (FINAL_webtech)

## Project Description
🌸Flowers Shop is a PHP + MySQL e-commerce web application for browsing, searching, and purchasing flowers. It includes a customer-facing storefront and an admin panel for managing products, orders, and site content.

## Install / Run Instructions
1. **Requirements**
   - PHP 7.4+ (with `mysqli` enabled)
   - MySQL/MariaDB
   - A local web server (Apache/Nginx or PHP built-in server)

2. **Database setup**
   - Create a database named `endterm` (or update `db.php` with your preferred name).
   - Import the schema and seed data:
     ```sh
     mysql -u root -p endterm < admin/install.sql
     ```

3. **Configure DB connection**
   - Edit `db.php` if needed:
     ```php
     $host = "127.0.0.1";
     $user = "root";
     $pass = "";
     $dbname = "endterm";
     $port = 3306;
     ```

4. **Run the app**
   - **Option A (Apache/Nginx):** point your document root to `/workspace/FINAL_webtech`.
   - **Option B (PHP built-in server):**
     ```sh
     php -S 0.0.0.0:8000
     ```
     Then open `http://localhost:8000/front/flowers.php`.

## Admin Credentials
Seeded admin user (from `admin/install.sql`):
- **Username:** `admin`
- **Email:** `admin@example.com`
- **Password:** The seed file stores a bcrypt hash placeholder. Set your own password by updating the `users.password` field in the database or re-seeding with a known hash.

## Features
- Product catalog with categories, stock, and pricing
- Search and filtering for flowers
- Shopping cart and checkout flow
- Favorites list (wishlist)
- Customer profiles and order history
- Admin dashboard for managing products, orders, and pages
- CMS pages editable from the admin panel
- Contact page and site settings management
