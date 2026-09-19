#!/bin/bash
set -e

echo "1. Mematikan aplikasi..."
php artisan down

echo "2. Menarik kode terbaru..."
git pull origin main

echo "3. Memperbarui dependensi..."
composer install --no-dev --optimize-autoloader

echo "4. Menjalankan migrasi..."
php artisan migrate --force

echo "5. Membersihkan cache..."
php artisan optimize:clear
php artisan optimize

echo "6. Membangun aset frontend..."
npm run build

echo "7. Menyalakan aplikasi..."
php artisan up

echo "Deployment selesai!"