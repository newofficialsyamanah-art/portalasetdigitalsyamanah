Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "  DEPLOY UPDATE KE SERVER VPS (139.190.99.209)" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/4] Mengompres file proyek..." -ForegroundColor Yellow
tar.exe --exclude="node_modules" --exclude=".git" --exclude="dist" -czf "portal-update.tar.gz" .

if (-not (Test-Path "portal-update.tar.gz")) {
    Write-Host "Gagal membuat file arsip!" -ForegroundColor Red
    exit 1
}

Write-Host "[2/4] Mengunggah file ke VPS 139.190.99.209..." -ForegroundColor Yellow
scp -i "$HOME\.ssh\id_rsa" portal-update.tar.gz syamanah@139.190.99.209:/home/syamanah/portal-update.tar.gz

Write-Host "[3/4] Mengekstrak file & me-restart service di VPS..." -ForegroundColor Yellow
ssh -i "$HOME\.ssh\id_rsa" syamanah@139.190.99.209 "tar -xzf /home/syamanah/portal-update.tar.gz -C /home/syamanah/portal-aset && rm -f /home/syamanah/portal-update.tar.gz && chmod -R 775 /home/syamanah/portal-aset/storage /home/syamanah/portal-aset/bootstrap/cache && sudo systemctl restart portal-syamanah && sudo systemctl status portal-syamanah --no-pager"

Write-Host "[4/4] Membersihkan file sementara..." -ForegroundColor Yellow
Remove-Item -Path "portal-update.tar.gz" -Force -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "===================================================" -ForegroundColor Green
Write-Host "  UPDATE BERHASIL DITERAPKAN DI SERVER!" -ForegroundColor Green
Write-Host "  Buka di browser: http://139.190.99.209/" -ForegroundColor Green
Write-Host "===================================================" -ForegroundColor Green

