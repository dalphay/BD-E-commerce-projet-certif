FROM php:7.2-fpm-alpine
WORKDIR "/application"

# Install selected extensions and other stuff
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && apk add --no-cache --update --virtual buildDeps $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del buildDeps