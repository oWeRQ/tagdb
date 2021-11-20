FROM node:14-alpine AS asset
WORKDIR /build
COPY package.json package-lock.json webpack.mix.js ./
RUN npm install
COPY resources resources
RUN npm run production

FROM php:8.0-fpm-alpine
RUN apk add --update --no-cache \
    dumb-init \
    bash \
    curl \
    nginx \
    zlib \
    libpng \
    libpng-dev \
    libzip \
    libzip-dev \
    && docker-php-ext-install gd zip pdo pdo_mysql > /dev/null \
    && apk del libpng-dev libzip-dev \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
WORKDIR /var/www/html
USER www-data
COPY --chown=www-data composer.json composer.lock ./
RUN mkdir -p \
    database/seeds \
    database/factories \
    storage/logs \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    && composer install
COPY --chown=www-data . .
COPY --chown=www-data --from=asset /build/public public
USER root
ENTRYPOINT ["/usr/bin/dumb-init", "--"]
CMD ["./docker/init.sh"]
