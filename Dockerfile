FROM arm64v8/php:7-zts-alpine3.15
RUN docker-php-ext-install mysqli
RUN mkdir  /var/www/myapp
COPY *.php *.html   /var/www/myapp/
