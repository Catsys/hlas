FROM php:8.3-fpm

# Устанавливаем необходимые расширения для работы с PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Устанавливаем Composer для управления зависимостями
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем файлы проекта
WORKDIR /var/www/symfony
COPY . /var/www/symfony

# Устанавливаем права для файлов
RUN chown -R www-data:www-data /var/www/symfony
