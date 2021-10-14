FROM php:7.4-apache

EXPOSE 80
EXPOSE 443

VOLUME [ "/var/www/cdn" ]

# Installs deps
RUN apt-get update -y && \
    apt-get install --no-install-recommends -y \
    certbot \
    ffmpeg \
    git \
    libpq-dev \
    python3-certbot-apache \
    unzip \
    zip 

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pgsql

# PHP Composer
COPY composer.json /var/www
COPY composer.lock /var/www
WORKDIR /var/www
RUN composer install

# Apache configs
RUN a2enmod rewrite ssl
RUN rm /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

COPY configs/main-default.conf /etc/apache2/sites-available/main.conf
COPY configs/cdn-default.conf /etc/apache2/sites-available/cdn.conf
RUN a2ensite main cdn

# For some reason the mounted volume needs special permissions
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY configs/php.ini /usr/local/etc/php/php.ini
COPY configs/apache2.conf /etc/apache2/apache2.conf
COPY php/ /var/www/php/
COPY public/ /var/www/html/