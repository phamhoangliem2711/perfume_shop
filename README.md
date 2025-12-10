# Shop Nước Hoa (Simple PHP)

This is a minimal Shop Nước Hoa built with plain PHP using PDO for MySQL. Các giao diện mặc định đã được Việt hóa.

## Structure

perfume_shop/
- config/database.php - Database connection class (default DB: `perfume_shop`)
- helpers.php - Common helpers (sessions, auth, db)
- api/ - Simple API endpoints (products, product, checkout, upload)
- public/ - Frontend pages (index, login, register, product, cart, checkout, logout)
- admin/ - Admin area (product CRUD, orders)
- db_init.sql - SQL file to create tables
- seed_admin.php - Helper to quickly create an admin user

## Setup

1. Create database and tables. Import `db_init.sql` into your MySQL server (phpMyAdmin or mysql CLI):

```sql
SOURCE path/to/perfume_shop/db_init.sql;
```

2. Update `config/database.php` with your database credentials if needed.

3. Create an admin user:

- Option 1: run `seed_admin.php` from browser: `http://localhost/perfume_shop/seed_admin.php?pwd=admin123&email=admin@shop.com`
- Option 2: use CLI: `php seed_admin.php admin123` (from the `perfume_shop` directory)

4. Start WAMP (if not already) and visit `http://localhost/perfume_shop/public/`.

## Notes

- This is intentionally minimal for demo purposes. For production, add CSRF protection, input validation, prepared statements (already used), and more robust file upload handling.
- The admin area uses the `role` column on the `users` table (values: `user` or `admin`).
- You can extend features like product variants (already implemented), inventory, or file management as needed.

