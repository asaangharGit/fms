# FMS — Financial Management System

Laravel 8 application covering the chart of accounts, general ledger, vouchers, banks and checkbooks, vendors, receivables, lease obligations, asset registration, items and reports.

In production it runs on cPanel with the **repository root as the document root** (root `index.php` plus `.htaccess`). The `.htaccess` therefore whitelists what browsers may load; see [Security](#security).

## Requirements

- PHP **8.2** (`composer.json` pins `config.platform.php` to 8.2)
- MySQL 5.7 or 8.0
- Composer 2
- PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `fileinfo`, `curl`

## Local setup (Laragon)

1. Switch Laragon to PHP 8.2: Menu → PHP → Version → `php-8.2.x`.
2. Install dependencies:
   ```
   composer install
   ```
3. Create the environment file and keys:
   ```
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   php artisan passport:keys
   ```
4. Create the `fmsdb` database, import the latest SQL dump (dumps are not in git), then run `php artisan migrate`.
5. Open `https://fms.test`, or run `serve.bat` and open `http://127.0.0.1:8010`.

`artisan82.bat <command>` runs artisan with PHP 8.2 even when another PHP version is first on `PATH`.

## Deploying to the server

```bash
cd ~/public_html/fms.agfltech.com
git pull
rm -f bootstrap/cache/*.php                 # stale package/provider caches
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan passport:keys                   # first time only (creates storage/oauth-*.key)
php artisan config:clear && php artisan view:clear
```

Before the first deploy on PHP 8.2, select PHP 8.2 for the domain in cPanel → MultiPHP Manager. cPanel rewrites only its own marked blocks in `.htaccess`.

## Security

- `.htaccess` serves only `public/`, `.well-known/` and `favicon.ico` from disk. Every other path goes to Laravel, so source code, `.env`, `.git`, backups, SQL dumps and logs cannot be downloaded. PHP runs only through `index.php`.
- Voucher attachments (`/uploads/{voucher}/{file}`) are served by `UploadController` to logged-in users only.
- In production `.env` must have `APP_ENV=production` and `APP_DEBUG=false`.
- Keep backups (`Backup/`, `*.zip`) **outside** `public_html` where possible.
- Laravel 8 is end-of-life and some framework advisories will never be patched for it. Plan an upgrade to a supported Laravel version.

## Not in version control

`.env`, `vendor/`, `Backup/` (DB dumps), `uploads/` and `public/uploads/` (user documents), logs and cPanel-generated files. See `.gitignore`.

## General Ledger API

Read-only API for Power BI: [`docs/api/general-ledger/README.md`](docs/api/general-ledger/README.md).
