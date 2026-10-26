FROM php:8.3-apache
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev libonig-dev libpng-dev libjpeg-dev libwebp-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp \
 && docker-php-ext-install gd pdo_pgsql mbstring \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*
# Render sends traffic to port 10000 by default
RUN sed -ri 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
 && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
 && printf 'upload_max_filesize=8M\npost_max_size=10M\nmemory_limit=256M\nexpose_php=Off\ndisplay_errors=Off\nlog_errors=On\n' > /usr/local/etc/php/conf.d/app.ini
COPY . /var/www/html/
RUN rm -rf /var/www/html/sql /var/www/html/Dockerfile /var/www/html/.dockerignore /var/www/html/README.md \
 && chown -R www-data:www-data /var/www/html
EXPOSE 10000
