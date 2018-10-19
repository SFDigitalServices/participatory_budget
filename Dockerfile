FROM php:7.2-apache

# Change document root to ./web to match new directory structure
ENV APACHE_DOCUMENT_ROOT /var/www/html/web
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

# install the PHP extensions we need
# install mysql-client to allow drush to be executed in the container
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libpq-dev mysql-client \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
  && docker-php-ext-install gd mbstring pdo pdo_mysql pdo_pgsql zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    &&  php composer-setup.php --filename=composer --install-dir /usr/local/bin \
    && php -r "unlink('composer-setup.php');" \
    && echo 'export COMPOSER_ALLOW_SUPERUSER=1' >> ~/.bashrc

WORKDIR /var/www/html
