FROM php:8.2-apache

# Install ekstensi mysqli supaya bisa konek ke database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Salin semua file project ke dalam web root apache
COPY . /var/www/html/

# Set permission biar aman
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install mysqli
