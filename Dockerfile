FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN ["apt-get", "update", "-y"]
RUN ["apt-get", "install", "-y", "openssl", "zip", "unzip", "git", "nodejs", "npm"]

RUN ["docker-php-ext-install", "pdo", "pdo_mysql", "bcmath"]

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html
RUN ["composer", "install", "--no-dev"]
RUN ["npm", "install"]
RUN ["npm", "run", "build"]