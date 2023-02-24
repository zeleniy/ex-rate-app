FROM php:8.1-cli-alpine

RUN docker-php-ext-install pcntl

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/bin/composer

WORKDIR /var/www/exchange-rate

CMD ["php", "/var/www/exchange-rate/src/Server/index.php", "start"]