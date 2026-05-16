#!/bin/bash

echo "Removing old storage symlink..."
rm -rf public/storage

echo "Running database migrations..."
php artisan migrate --force || echo "WARNING: Some migrations may have failed, continuing..."

echo "Seeding the database..."
php artisan db:seed --force || echo "WARNING: Seeding issue, continuing..."

echo "Creating storage symlink..."
php artisan storage:link || echo "WARNING: Storage link issue, continuing..."

echo "Starting Queue Worker in the background..."
php artisan queue:work --daemon &

echo "Starting Laravel Web Server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
