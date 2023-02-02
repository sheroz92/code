FROM composer:latest AS composer
FROM php:7.4-fpm
ARG PHP_USER_ID=1000
ARG PHP_USER_NAME='site-user'

ENV PHP_USER_ID=${PHP_USER_ID}
ENV PHP_USER_NAME=${PHP_USER_NAME}

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update && \
     apt-get install -y \
         libzip-dev \
         zlib1g-dev \
         libpng-dev \
         && docker-php-ext-install zip && \
         apt-get update && \
         rm -rf /var/lib/apt/lists/* && \
         docker-php-ext-install gd mysqli pdo pdo_mysql zip && \
         docker-php-ext-enable gd pdo_mysql zip

#RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
#RUN printf "\n" | pecl install imagick
#RUN docker-php-ext-enable imagick

RUN echo $PHP_USER_NAME
RUN useradd -m -u $PHP_USER_ID -o -s /bin/bash $PHP_USER_NAME || echo "User already exists."
RUN usermod -u $PHP_USER_ID $PHP_USER_NAME \
  && groupmod -g $PHP_USER_ID $PHP_USER_NAME \
  && mkdir /app \
  && chown -R $PHP_USER_ID:$PHP_USER_ID /app

WORKDIR /app
