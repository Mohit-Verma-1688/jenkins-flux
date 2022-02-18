FROM php:7.2-fpm
RUN docker-php-ext-install mysqli
RUN mkdir -p /var/www/myapp/
COPY *.php   /var/www/myapp/
