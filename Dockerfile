# PHP + Apache image
FROM php:8.2-apache

# SQLite ve PDO desteği
RUN docker-php-ext-install pdo pdo_sqlite

# Apache mod_rewrite aktif et
RUN a2enmod rewrite

# Proje dosyalarını container içine kopyala
COPY . /var/www/html/

# Upload ve DB klasörlerini writable yap
RUN mkdir -p /var/www/html/uploads && \
    mkdir -p /var/www/html/db && \
    chown -R www-data:www-data /var/www/html/uploads /var/www/html/db

# Apache varsayılan port
EXPOSE 80
