FROM php:7.2-apache
WORKDIR /var/www/html
COPY ./ /var/www/html
COPY entry.sh /entry.sh
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get update \
    && apt-get install -y zip unzip software-properties-common nodejs npm \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers
RUN npm install npm -g --silent && \
    npm install n -g --silent && \
    n lts
CMD ["apache2-foreground"]
ENTRYPOINT ["/entry.sh"]
