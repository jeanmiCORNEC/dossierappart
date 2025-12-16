FROM php:8.2-fpm

# 1. Installation des dépendances système
# On ajoute nginx et supervisor car c'est un conteneur "tout-en-un"
# On ajoute ghostscript et imagemagick pour tes besoins PDF/Images
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nginx \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libmagickwand-dev \
    ghostscript \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensions PHP requises par Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Installation de Node.js (Version 20 pour Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 5. Configuration Nginx/PHP/Supervisor
# On copie les fichiers de config que tu as récupérés du dossier 'docker/'
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# 6. Copie du code source
WORKDIR /var/www
COPY . .

# 7. Permissions
# On donne les droits à www-data pour qu'il puisse écrire les logs et le cache
RUN chown -R www-data:www-data /var/www

# 8. Démarrage
EXPOSE 80
CMD ["/usr/bin/supervisord"]
