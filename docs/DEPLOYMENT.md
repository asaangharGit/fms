# Deploying FMS to the cPanel server

Server path: `~/public_html/fms.agfltech.com`. The document root is the project root.

This runbook covers the first deploy of the PHP 8.2 / security release, plus routine deploys afterwards. Run the commands in **cPanel → Terminal** (or SSH). Plan for a short maintenance window (about 10–15 minutes) at a quiet time.

---

## 0. Checks before you start (cPanel UI)

| Check | Where |
|---|---|
| PHP **8.2** (`ea-php82`) is installed | cPanel → MultiPHP Manager (the version list) |
| PHP 8.2 extensions: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `fileinfo`, `curl` | cPanel → Select PHP Version / MultiPHP INI Editor (or ask the host) |
| Terminal or SSH access | cPanel → Terminal |
| GitHub access from the server: a read-only **Deploy key** (repo → Settings → Deploy keys) or a Personal Access Token | GitHub |

Find the PHP 8.2 and Composer binaries on the server. You will use them in every step below:

```bash
ls /opt/cpanel/ea-php82/root/usr/bin/php     # PHP 8.2 CLI
which composer || ls /opt/cpanel/composer/bin/composer
```

```bash
PHP=/opt/cpanel/ea-php82/root/usr/bin/php
COMPOSER=$(which composer || echo /opt/cpanel/composer/bin/composer)
cd ~/public_html/fms.agfltech.com
```

> The plain `php` command on cPanel is often a different version. Always use `$PHP`.

---

## 1. Backups (do not skip)

```bash
mkdir -p ~/deploy-backups
# Database: phpMyAdmin → Export works too
mysqldump -u <db_user> -p <db_name> > ~/deploy-backups/fmsdb-$(date +%F-%H%M).sql
# Code including vendor/ (so you can roll back), without uploads, backups and archives
tar --exclude=./uploads --exclude=./Backup --exclude='./*.zip' --exclude=./ziqV6jjg \
    -czf ~/deploy-backups/fms-code-$(date +%F-%H%M).tar.gz .
```

Backups go to `~/deploy-backups`, which is **outside** `public_html`.

---

## 2. Maintenance mode

```bash
php artisan down          # old code: use the CLI version the site runs on today (PHP 7.4)
```

If this fails (for example because `php` is already PHP 8), run `/opt/cpanel/ea-php74/root/usr/bin/php artisan down` instead.

---

## 3. Get the code (first deploy only: attach git)

The folder is not a git repository yet. These commands attach it without touching any files:

```bash
git init
git remote add origin git@github.com:asaangharGit/fms.git     # or the https URL + token
git fetch origin main
git reset origin/main
git checkout -B main
git branch -u origin/main

git status          # files listed as modified differ between the server and the repo
git diff --stat
```

Look at the list. If a file was edited directly on the server and that change must be kept, copy it aside first. Then take the repository version:

```bash
git checkout -- .
```

Ignored server files are not touched: `.env`, `uploads/`, `Backup/`, `vendor/`, zips, logs, `.well-known/`.

For later deploys this whole step is just `git pull`.

---

## 4. Dependencies (PHP 8.2)

```bash
rm -f bootstrap/cache/*.php                       # stale package/provider caches
$PHP -d memory_limit=-1 $COMPOSER install --no-dev --optimize-autoloader --no-interaction
```

---

## 5. `.env` (production values)

Edit `.env` and make sure it contains:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://fms.agfltech.com
SESSION_SECURE_COOKIE=true
```

Optional General Ledger API settings are listed in `docs/api/general-ledger/README.md`.

---

## 6. PHP version, database, caches

The repository `.htaccess` already selects `ea-php82`. Confirm that cPanel → MultiPHP Manager shows **PHP 8.2** for `fms.agfltech.com`.

```bash
$PHP artisan migrate --force          # api_clients table, GL indexes, roles → InnoDB
[ -f storage/oauth-private.key ] || $PHP artisan passport:keys
$PHP artisan config:clear && $PHP artisan route:clear && $PHP artisan view:clear
chmod -R 775 storage bootstrap/cache
$PHP artisan up
```

Do **not** run `config:cache`: some legacy code calls `env()` at runtime.

---

## 7. Cron jobs (important)

cPanel → Cron Jobs: every command that runs `artisan` must use the PHP 8.2 binary, for example:

```
* * * * * /opt/cpanel/ea-php82/root/usr/bin/php /home/agfltech/public_html/fms.agfltech.com/artisan schedule:run >> /dev/null 2>&1
```

A cron job still using the old PHP version fails with the new code.

---

## 8. Verify

In the browser: log in, then open the dashboard, Vouchers, a voucher with an attachment, Reports → General Ledger and the Trial Balance.

From the terminal, these must be **blocked** (403 or 404):

```bash
for u in /.env /composer.json /storage/logs/laravel.log /Backup/backup.php /.git/config; do
  curl -s -o /dev/null -w "%{http_code}  $u\n" "https://fms.agfltech.com$u"; done
```

These must **work**:

```bash
curl -s -o /dev/null -w "%{http_code} login\n" https://fms.agfltech.com/login     # 200
curl -s -o /dev/null -w "%{http_code} api\n"   https://fms.agfltech.com/api/v1/gl # 401 (no key)
```

Power BI key:

```bash
$PHP artisan api-client:create "Power BI - Finance" --expires=2027-10-31
```

---

## 9. Rollback (if something is wrong)

```bash
php artisan down 2>/dev/null
tar -xzf ~/deploy-backups/fms-code-<timestamp>.tar.gz -C ~/public_html/fms.agfltech.com
```

Then set the domain back to PHP 7.4 in MultiPHP Manager. The restored `.htaccess` already says `ea-php74`. Run `php artisan up` (PHP 7.4).

The database does not need restoring: the migrations only add a table and indexes and change the `roles` engine, and the old code works with all of that.

---

## 10. After the first deploy

- Change the database password (cPanel → MySQL Databases), then update `.env` and `Backup/backup.php`. That password and the SQL dumps were publicly reachable before this release.
- Move `Backup/` output, `fms_*.zip` and `ziqV6jjg` out of `public_html`. They are blocked now, but keeping them outside the web root is safer.
- Review the role permissions in Users/Roles. Every role currently has user and role management.

## Routine deploys

```bash
cd ~/public_html/fms.agfltech.com
php artisan down
git pull
$PHP -d memory_limit=-1 $COMPOSER install --no-dev --optimize-autoloader --no-interaction
$PHP artisan migrate --force
$PHP artisan config:clear && $PHP artisan view:clear
$PHP artisan up
```

Never edit code directly on the server. Change it locally, push, then pull on the server.
