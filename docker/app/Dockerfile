# Luan Erazo personal site · LOCAL DEVELOPMENT image.
#
# PHP 8.4 to match the Hostinger target (hPanel allows up to 8.5).
# If the PHP version in hPanel changes, change it here too.
# Node is NOT in this image: Vite runs in the `node` service (compose.yml).
FROM php:8.4-cli-bookworm

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl gd zip bcmath exif opcache pcntl \
    && apt-get update && apt-get install -y --no-install-recommends git unzip mariadb-client \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/app/php.ini /usr/local/etc/php/conf.d/zz-luane.ini

WORKDIR /var/www
