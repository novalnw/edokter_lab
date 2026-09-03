FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    gnupg \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update && apt-get install -y \
    apache2 \
    php8.2 \
    libapache2-mod-php8.2 \
    php8.2-mysqli \
    php8.2-pdo \
    php8.2-mysql \
    php8.2-gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2dismod -f mpm_event mpm_worker mpm_itk || true
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

# Hapus index.html default bawaan Apache Ubuntu supaya tidak menutupi index.php kita

RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY . /var/www/html/

EXPOSE ${PORT}
CMD ["apache2ctl", "-D", "FOREGROUND"]
