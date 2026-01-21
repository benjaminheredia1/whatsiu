FROM php:8.4-fpm

# Instalar dependencias del sistema y Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nginx \
    supervisor \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP (Producción)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Instalar dependencias de Node y compilar assets
RUN npm install && npm run build

# Copiar configuración de Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copiar configuración de Supervisor
RUN echo '[supervisord]' > /etc/supervisor/conf.d/laravel.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '[program:php-fpm]' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'command=/usr/local/sbin/php-fpm' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '[program:nginx]' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'command=/usr/sbin/nginx -g "daemon off;"' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/laravel.conf

# Dar permisos a las carpetas de almacenamiento
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto 80
EXPOSE 80

# Script de inicio
CMD php artisan migrate --force && /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
