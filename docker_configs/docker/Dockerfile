FROM php:8.3-fpm

# Устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y \
    libpq-dev \
    mariadb-client \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем проект
WORKDIR /var/www/symfony
COPY . /var/www/symfony

# Права доступа
RUN chown -R www-data:www-data /var/www/symfony
