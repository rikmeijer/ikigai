FROM php:8.3
RUN ["apt-get", "update", "-y"]
RUN ["apt-get", "install", "-y", "openssl", "zip", "unzip", "git"]
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN ["docker-php-ext-install", "pdo"]
WORKDIR /app
COPY . /app
RUN ["composer", "install"]

CMD ["php", "artisan", "serve", "--host=${SERVE_HOST}", "--port=${SERVE_PORT}"]
EXPOSE ${SERVE_PORT}