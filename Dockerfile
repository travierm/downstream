ARG PHP_VERSION=${PHP_VERSION:-7.4}

FROM php:${PHP_VERSION}-fpm-alpine AS php-system-setup

# Install system dependencies
RUN apk add --no-cache dcron busybox-suid libcap curl zip unzip git libc6-compat

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions intl bcmath gd pdo_mysql pdo_pgsql opcache redis uuid exif pcntl zip

# Install supervisord implementation
COPY --from=ochinchina/supervisord:latest /usr/local/bin/supervisord /usr/local/bin/supervisord

# Install caddy
COPY --from=caddy:2.2.1 /usr/bin/caddy /usr/local/bin/caddy

# Install composer
COPY --from=composer/composer:2 /usr/bin/composer /usr/local/bin/composer

FROM php-system-setup AS app-setup

# RUN addgroup -S $NON_ROOT_GROUP && adduser -S $NON_ROOT_USER -G $NON_ROOT_GROUP
# RUN addgroup $NON_ROOT_USER wheel

RUN apk add --update nodejs npm
RUN npm install -g yarn
RUN adduser -D -h /home/container container

USER container
ENV USER=container HOME=/home/container
WORKDIR /home/container

# Start app
EXPOSE 80
COPY ./.deploy/entrypoint.sh /

ENTRYPOINT ["/bin/ash", "/entrypoint.sh"]
