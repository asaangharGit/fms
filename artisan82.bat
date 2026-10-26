@echo off
REM Run artisan with the project PHP version (8.2), whatever "php" is on PATH.
REM Usage: artisan82 api-client:create "Power BI" --expires=2027-04-30
cd /d "%~dp0"
"D:\laragon\bin\php\php-8.2.19-nts-Win32-vs16-x64\php.exe" artisan %*
