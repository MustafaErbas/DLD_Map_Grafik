FROM php:8.2-apache

# PHP eklentileri
RUN docker-php-ext-install mysqli pdo pdo_mysql

# mod_rewrite
RUN a2enmod rewrite

# DocumentRoot’u public olarak ayarla
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Projeyi kopyala
COPY . /var/www/html/

# .htaccess çalışsın
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
