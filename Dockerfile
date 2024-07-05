FROM php:8.3-cli-bullseye

ARG SERVE_PORT=8080
ARG SERVE_HOST="0.0.0.0:$SERVE_PORT"

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "libmcrypt-dev", "libzip-dev", "zip"]
RUN ["docker-php-ext-install", "mbstring"]
RUN ["docker-php-ext-install", "sockets"]

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /ikigai
COPY . /ikigai
RUN ["chown", "www-data:www-data", "-R" , "."]

EXPOSE $SERVE_PORT

# Set the user
USER www-data

RUN ["composer", "install", "--no-dev"]
ENTRYPOINT ["rr", "serve"]
