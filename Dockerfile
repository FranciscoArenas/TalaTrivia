FROM php:8.2-fpm
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    zip unzip nodejs npm supervisor \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
 && pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

COPY --chown=www:www composer.json composer.lock* ./

RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

COPY --chown=www:www . .

RUN composer run-script post-autoload-dump --ansi \
 && php artisan storage:link --no-interaction \
 && chown -R www:www /var/www \
 && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

USER www
EXPOSE 9000
CMD ["php-fpm"]