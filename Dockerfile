# Base image
FROM php:8.1-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy and set permissions for the wait-for-db script
COPY ./wait-for-db.sh /usr/bin/wait-for-db.sh
RUN chmod +x /usr/bin/wait-for-db.sh

# Expose application port
EXPOSE 8000

# Command to start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
