ARG ALPINE_VERSION=3.14
# FROM alpine:${ALPINE_VERSION}
FROM php:8.2-fpm-alpine

LABEL Maintainer="Travier Moorlag"
LABEL Description="Lightweight container for running Laravel API's"

# Setup document root
WORKDIR /var/www/html

# Install packages and remove default server definition
RUN apk add --no-cache \
  curl \
  nginx \
  mysql-client \
  php82 \
  php82-ctype \
  php82-curl \
  php82-dom \
  php82-fpm \
  php82-gd \
  php82-intl \
  php82-json \
  php82-mbstring \
  php82-mysqli \
  php82-opcache \
  php82-openssl \
  php82-phar \
  php82-session \
  php82-xml \
  php82-xmlreader \
  php82-zlib \
  php82-fileinfo \
  php82-tokenizer \
  php82-xmlwriter \
  php82-pdo \
  php82-pdo_mysql \
  supervisor

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli

# Create symlink so programs depending on `php` still function
# RUN ln -s /usr/bin/php8 /usr/bin/php

# Configure nginx
COPY .deploy/config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY .deploy/config/fpm-pool.conf /etc/php82/php-fpm.d/www.conf
COPY .deploy/config/php.ini /etc/php82/conf.d/custom.ini

# Configure supervisord
COPY .deploy/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /var/www/html /run /var/lib/nginx /var/log/nginx

# Switch to use a non-root user from here on
USER nobody

# Add application
COPY --chown=nobody . /var/www/html/
COPY .env.prod /var/www/html/.env

# Expose the port nginx is reachable on
EXPOSE 8080

# Run Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-interaction --no-progress
RUN php -v

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf", "--silent"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping