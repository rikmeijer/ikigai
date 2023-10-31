FROM php:8.2-cli-bullseye

ARG SERVE_PORT=8000
ARG SERVE_HOST="0.0.0.0:$SERVE_PORT"

RUN apt-get update && apt-get install -y \
libmcrypt-dev \
&& docker-php-ext-install -j$(nproc) pcntl

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
COPY . /app
RUN ["chown", "www-data:www-data", "-R" , "."]

EXPOSE $SERVE_PORT

# Set the user
USER www-data

RUN ["composer", "install", "--no-dev"]
RUN ["composer", "serve"]
