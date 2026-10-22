FROM php:8.3-apache
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev libonig-dev \
 && docker-php-ext-install pdo_pgsql mbstring \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*
# Render sends traffic to port 10000 by default
RUN sed -ri 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
 && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
COPY . /var/www/html/
RUN rm -rf /var/www/html/sql /var/www/html/Dockerfile /var/www/html/.dockerignore /var/www/html/README.md \
 && chown -R www-data:www-data /var/www/html
EXPOSE 10000
