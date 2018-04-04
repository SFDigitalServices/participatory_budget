FROM php:7.1-apache

RUN a2enmod rewrite

# install the PHP extensions we need
# install mysql-client to allow drush to be executed in the container
RUN apt-get update && apt-get install -y libpng12-dev libjpeg-dev libpq-dev mysql-client \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
  && docker-php-ext-install gd mbstring pdo pdo_mysql pdo_pgsql zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    &&  php composer-setup.php --filename=composer --install-dir /usr/local/bin \
    && php -r "unlink('composer-setup.php');" \
    && echo 'export COMPOSER_ALLOW_SUPERUSER=1' >> ~/.bashrc

WORKDIR /var/www/html
