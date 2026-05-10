FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev nodejs npm nginx \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Копируем Laravel из src/
COPY src/ .

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Nginx конфиг
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Права
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php-fpm -D && \
    nginx -g "daemon off;"