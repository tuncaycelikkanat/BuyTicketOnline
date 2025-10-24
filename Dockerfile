FROM php:8.2-apache

# Sistem bağımlılıklarını yükle
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    pkg-config \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Proje dosyalarını kopyala
COPY . /var/www/html/

# Upload ve db klasörlerini writable yap
RUN mkdir -p /var/www/html/uploads && \
    mkdir -p /var/www/html/db && \
    chown -R www-data:www-data /var/www/html/uploads /var/www/html/db

EXPOSE 80
