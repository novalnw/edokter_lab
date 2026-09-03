FROM php:8.2-apache

# Install ekstensi mysqli dan pdo
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Sesuaikan port Apache agar kompatibel dengan Railway (menggunakan PORT dinamis)
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Salin semua file project ke direktori web root Apache
COPY . /var/www/html/
