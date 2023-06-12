FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libpq-dev libpng-dev libjpeg-dev libfreetype6-dev  \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install intl opcache pdo pdo_pgsql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pdo pdo_mysql

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . /var/www/html


CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/index.php"]
