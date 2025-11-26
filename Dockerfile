# Resmi PHP ve Apache sürümünü kullan
FROM php:8.2-apache

# Gerekli PHP eklentilerini kur (Örn: MySQL bağlantısı için mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache mod_rewrite'ı aktifleştir (SEO dostu URL kullanıyorsan gerekli)
RUN a2enmod rewrite

# Proje dosyalarını sunucuya kopyala
COPY . /var/www/html/

# Port 80'i dışarıya aç
EXPOSE 80