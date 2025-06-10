# Usa una imagen base de PHP-FPM
FROM php:8.2-fpm

# Paso 1: Actualiza la lista de paquetes e instala las dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    --no-install-recommends \ # Para reducir el tamaño de la imagen instalando solo lo estrictamente necesario
    && rm -rf /var/lib/apt/lists/* # Limpia la caché de apt para reducir el tamaño de la imagen final

# Paso 2: Configura e instala la extensión GD
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd

# Paso 3: Instala otras extensiones de PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring

# Copia el código de tu aplicación al contenedor
WORKDIR /var/www/html
COPY . /var/www/html

# Define el puerto en el que PHP-FPM escuchará
EXPOSE 9000

# Comando para iniciar PHP-FPM
CMD ["php-fpm"]