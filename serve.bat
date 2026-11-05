@echo off
title Portal Aset Digital Syamanah (Laravel 11)
cls
echo ===================================================
echo   PORTAL ASET & KREDENSIAL DIGITAL SYAMANAH
echo   Ditenagai Laravel 11 & PHP 8.2 (XAMPP)
echo ===================================================
echo.
echo Server berjalan di: http://localhost:3000
echo Tekan Ctrl + C untuk menghentikan server.
echo.

if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" artisan serve --port=3000
) else (
    php artisan serve --port=3000
)

pause

