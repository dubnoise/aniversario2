FROM php:8.3-apache

# Instalar extensiones PHP necesarias y utilidades
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copiar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar Node.js para compilar assets (Tailwind/Vite)
COPY --from=node:22-alpine /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-alpine /usr/local/bin/npm /usr/local/bin/npm

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP y compilar frontend
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci \
    && npm run build \
    && rm -rf node_modules

# Apuntar Apache a public/ y dar permisos a storage y bootstrap/cache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar puerto dinámico de Render y arrancar Apache
CMD sed -i "s/Listen 80/Listen ${PORT:-8080}/g" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT:-8080}/g" /etc/apache2/sites-available/000-default.conf \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && apache2-foreground
