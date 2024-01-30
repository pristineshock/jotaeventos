FROM composer as builder
WORKDIR /api/
COPY /api/composer.* ./
RUN composer install


FROM php:8.1-apache-buster

COPY --from=builder /api/vendor /var/www/html/api/vendor

COPY . /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite headers deflate

RUN service apache2 restart