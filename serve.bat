@echo off
REM Local dev server for FMS on PHP 8.2 (alternative to https://fms.test).
REM Serves the project root, same as production (assets live under /public/...).
cd /d "%~dp0"
set PHP82=D:\laragon\bin\php\php-8.2.19-nts-Win32-vs16-x64\php.exe
echo FMS running at http://127.0.0.1:8010  (Ctrl+C to stop)
"%PHP82%" -S 127.0.0.1:8010 -t .
