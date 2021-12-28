FROM php:7.3.28-apache

ARG APCU_VERSION=5.1.11


# COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
# COPY start-apache /usr/local/bin

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install APCu and APC backward compatibility
RUN pecl install apcu-5.1.21 \
    && docker-php-ext-enable apcu --ini-name 10-docker-php-ext-apcu.ini
    
RUN a2enmod rewrite &&\
    service apache2 restart

RUN php --ini
RUN php --info | grep apc

# # Copy application source
# COPY src /var/www/
# RUN chown -R www-data:www-data /var/www

#CMD ["service apache2 start"]