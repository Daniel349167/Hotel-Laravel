# Usar la imagen oficial de PHP con soporte para Apache
FROM php:8.2-apache

# Instalar extensiones requeridas (pdo_mysql, gd y zip)
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar los archivos del proyecto
COPY . /var/www/html

# Cambiar el DocumentRoot de Apache a la carpeta public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Crear los directorios de caché y darles permisos correctos
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Habilitar mod_rewrite en Apache
RUN a2enmod rewrite

# Configuración de permisos de archivos
RUN chmod -R 755 /var/www/html

# Ejecutar Composer para instalar dependencias
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Configuración de la carpeta de trabajo
WORKDIR /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio de Apache
CMD ["apache2-foreground"]