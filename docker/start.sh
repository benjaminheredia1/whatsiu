#!/bin/bash

# Run migrations
php artisan migrate --force

# Start supervisor (which manages php-fpm and nginx)
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
