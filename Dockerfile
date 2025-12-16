FROM php:8.2-fpm

# 1. Installation des dépendances système
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
    imagemagick \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. FIX MAGICK : Alias et Policy PDF
# On crée un lien pour que 'magick' appelle 'convert'
# On autorise la manipulation des PDF dans policy.xml
RUN ln -sf /usr/bin/convert /usr/bin/magick \
    && find /etc -name "policy.xml" -exec sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/g' {} +

# 3. Extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 6. Configs
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# 7. Build
WORKDIR /var/www
COPY . .
RUN chown -R www-data:www-data /var/www

EXPOSE 80
CMD ["/usr/bin/supervisord"]