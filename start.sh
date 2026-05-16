#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "Removing old storage symlink..."
rm -rf public/storage

echo "Running database migrations..."
php artisan migrate --force

echo "Seeding the database..."
php artisan db:seed --force

echo "Creating storage symlink..."
php artisan storage:link

echo "Starting Queue Worker in the background..."
php artisan queue:work --daemon &

echo "Starting Laravel Web Server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
