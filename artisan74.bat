@echo off
REM Run artisan with PHP 7.4 (Laravel 8.62 in this project crashes on PHP 8.1+).
REM Usage: artisan74 api-client:create "Power BI" --expires=2027-04-30
cd /d "%~dp0"
"D:\laragon\bin\php\php-7-4-13-no-thread-safe\php.exe" artisan %*
