@echo off
title Update & Deploy Portal Syamanah ke VPS
cls
echo ===================================================
echo   DEPLOY UPDATE KE SERVER VPS (139.190.99.209)
echo ===================================================
echo.

echo [1/4] Mengompres file proyek (tanpa node_modules/.git)...
tar.exe --exclude="node_modules" --exclude=".git" --exclude="dist" -czf "portal-update.tar.gz" .

if not exist "portal-update.tar.gz" (
    echo Gagal membuat file arsip update!
    pause
    exit /b
)

echo.
echo [2/4] Mengunggah file ke VPS 139.190.99.209...
scp -i "C:\Users\SYAMANAH\.ssh\id_rsa" portal-update.tar.gz syamanah@139.190.99.209:/home/syamanah/portal-update.tar.gz

echo.
echo [3/4] Mengekstrak file & me-restart service di VPS...
ssh -i "C:\Users\SYAMANAH\.ssh\id_rsa" syamanah@139.190.99.209 "tar -xzf /home/syamanah/portal-update.tar.gz -C /home/syamanah/portal-aset && rm -f /home/syamanah/portal-update.tar.gz && chmod -R 775 /home/syamanah/portal-aset/storage /home/syamanah/portal-aset/bootstrap/cache && sudo systemctl restart portal-syamanah && sudo systemctl status portal-syamanah --no-pager"

echo.
echo [4/4] Membersihkan file sementara lokal...
del portal-update.tar.gz

echo.
echo ===================================================
echo   UPDATE BERHASIL DITERAPKAN DI SERVER!
echo   Buka di browser: http://139.190.99.209/
echo ===================================================
echo.
pause

