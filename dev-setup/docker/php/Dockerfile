ARG PHP_VER=8.0

FROM php:${PHP_VER}-cli

RUN apt-get update -y
RUN apt-get install vim wget curl nano zip iputils-ping git -y

# Install Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install xdebug 3
RUN pecl install xdebug \
    && docker-php-ext-install sockets \
	&& docker-php-ext-enable sockets xdebug

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Stay-alive script so container doesn't exit
COPY ./stay-alive.sh /root/stay-alive.sh
RUN chmod +x /root/stay-alive.sh

WORKDIR /app
CMD [ "sh", "/root/stay-alive.sh" ]
