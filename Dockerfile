FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get install zip libzip-dev -y
RUN apt-get install iputils-ping -y
RUN docker-php-ext-install zip

RUN rm -rf /var/cache/apk/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
 
COPY . /var/www/html
WORKDIR /var/www/html
RUN composer update
RUN composer install
EXPOSE 80
CMD cd /var/www/html
CMD php -S 0.0.0.0:80