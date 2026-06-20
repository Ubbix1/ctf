FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# ✅ Enable Apache rewrite module
RUN a2enmod rewrite

# ✅ Allow .htaccess to work
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
 && a2enconf override

COPY . /var/www/html/
