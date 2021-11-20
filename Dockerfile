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
COPY --chown=www-data . .
RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views \
    && composer install
USER root
ENTRYPOINT ["/usr/bin/dumb-init", "--"]
CMD ["./docker/init.sh"]
