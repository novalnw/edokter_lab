FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Matikan MPM event/worker yang bentrok, paksa prefork untuk PHP
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork rewrite

# Bikin Apache dengerin port dinamis Railway
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

COPY . /var/www/html/
