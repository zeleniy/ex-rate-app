FROM php:8.1-fpm-alpine

RUN apk update
RUN apk add $PHPIZE_DEPS
#RUN apk add freetype-dev \
#    libjpeg-turbo-dev \
#    libpng-dev \
#    libwebp-dev \
#    zlib-dev \
#    libxpm-dev \
#    libxml2-dev \
#    libzip-dev \
#    oniguruma-dev \
#    postgresql-dev
#RUN docker-php-ext-configure gd --enable-gd --with-jpeg --with-freetype --with-webp --with-xpm
#RUN docker-php-ext-install gd pdo_mysql zip mbstring

RUN pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug

#RUN apk del libpng-dev

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/bin/composer

WORKDIR /var/www/exchange-rate

CMD ["php-fpm"]