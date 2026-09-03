FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Nonaktifkan modul MPM event/worker lalu aktifkan prefork
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork rewrite

# Konfigurasi port Apache agar otomatis ngikutin port dinamis Railway
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

COPY . /var/www/html/
