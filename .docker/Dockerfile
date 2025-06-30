FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and Xdebug prerequisites
RUN apk add --no-cache \
    bash \
    build-base \
    autoconf \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    nginx \
    supervisor \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy custom PHP and Xdebug configurations
COPY ./php/php.ini /usr/local/etc/php/conf.d/98-php.ini
COPY ./php/xdebug.ini /usr/local/etc/php/conf.d/99-xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p /var/www/html/var/log && \
    mkdir -p /var/www/html/public && \
    chown -R www-data:www-data /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/public

EXPOSE 9000
CMD ["php-fpm"]
