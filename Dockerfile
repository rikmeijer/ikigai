FROM php:8.3-cli-bullseye

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "zip", "libzip-dev", "libonig-dev"]
RUN ["docker-php-ext-install", "zip", "mbstring", "sockets"]

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /ikigai
WORKDIR /ikigai
RUN ["chown", "www-data:www-data", "-R" , "."]

EXPOSE 8080

# Set the user
USER www-data


RUN ["composer", "install", "--no-dev"]
ENTRYPOINT ["./vendor/bin/rr", "serve"]
