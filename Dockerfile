# Use an official PHP runtime as a parent image
FROM php:8.2-fpm

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    redis-server \
    libssl-dev \
    libsasl2-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl sockets

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install Laravel dependencies (including development dependencies for testing purposes)
RUN composer install --prefer-dist --optimize-autoloader

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Expose port 8000
EXPOSE 8000

# Command to start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
