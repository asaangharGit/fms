@echo off
REM Local dev server for FMS on PHP 7.4 (Laravel 8.62 does not run on PHP 8.1+).
REM Serves the project root, same as production (assets live under /public/...).
cd /d "%~dp0"
set PHP74=D:\laragon\bin\php\php-7-4-13-no-thread-safe\php.exe
echo FMS running at http://127.0.0.1:8010  (Ctrl+C to stop)
"%PHP74%" -S 127.0.0.1:8010 -t .
