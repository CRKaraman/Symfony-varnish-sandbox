FROM php:7.4-fpm
RUN apt-get update && apt-get install -y git
RUN docker-php-ext-install pdo_mysql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD php-fpm
EXPOSE 9000