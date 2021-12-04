#!/bin/bash

su www-data -s /bin/sh -c 'php artisan package:discover -n && php artisan migrate -n'

nginx -c /var/www/html/docker/nginx.conf &
php-fpm -y /var/www/html/docker/php-fpm.conf &
wait -n
exit 1
