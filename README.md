# FMS — Financial Management System

Laravel 8 application (chart of accounts, general ledger, vouchers, banks/checkbooks,
vendors, receivables, lease obligations, asset registration, items, reports).
Production runs on cPanel (PHP 7.4) at the repository root (root `index.php` + `.htaccess`).

## Requirements

- PHP **7.4** (Laravel 8.62 in `composer.lock` does not boot on PHP 8.1+)
- MySQL 5.7 / 8.0
- Composer 2
- Extensions: pdo_mysql, mbstring, openssl, gd, zip, fileinfo

## Local setup (Laragon)

1. Switch Laragon to PHP 7.4 (Menu → PHP → Version → php-7.4.x).
2. Install dependencies:
   ```
   composer install
   ```
3. Create the environment file and app key:
   ```
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```
4. Create the `fmsdb` database and import the latest SQL dump (dumps are not in git).
5. Make sure `storage/` and `bootstrap/cache/` are writable, then open `http://fms.test`.

## Not in version control

`.env`, `vendor/`, `Backup/` (DB dumps), `uploads/` and `public/uploads/` (user documents),
logs and cPanel-generated files — see `.gitignore`.
