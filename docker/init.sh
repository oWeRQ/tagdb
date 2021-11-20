#!/bin/bash

nginx -c /var/www/html/docker/nginx.conf &
php-fpm -y /var/www/html/docker/php-fpm.conf &
wait -n
exit 1