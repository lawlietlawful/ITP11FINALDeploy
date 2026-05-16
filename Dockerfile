# Use the official PHP image with CLI
FROM php:8.4-cli

# Install system dependencies, including PostgreSQL development headers
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Clear out the apt cache to keep the image small
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install the necessary PHP extensions for Laravel and PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Copy the Composer binary from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /app

# Copy all the project files into the container
COPY . .

# Run Composer to install PHP dependencies (ignoring dev packages)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run NPM to install frontend dependencies and build assets
RUN npm install && npm run build

# Ensure Laravel's storage directories have the correct permissions
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    storage/app/public

RUN chmod -R 777 storage bootstrap/cache

# Start the application using Laravel's built-in server
# This will run migrations, link the storage, and start the server on the port Render provides
CMD rm -rf public/storage && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
