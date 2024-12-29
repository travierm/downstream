ARG ALPINE_VERSION=3.14
FROM php:8.2-fpm-alpine

LABEL Maintainer="Travier Moorlag"
LABEL Description="Lightweight container for running Laravel API's"

# Setup document root
WORKDIR /var/www/html

# Install packages and dependencies
RUN apk add --no-cache \
  curl \
  nginx \
  mysql-client \
  supervisor \
  libpng-dev \
  icu-dev \
  oniguruma-dev \
  libxml2-dev

RUN rm -f /usr/local/etc/php/conf.d/*pdo*.ini

# Install PHP extensions
RUN docker-php-ext-install \
  pdo \
  pdo_mysql \
  mysqli \
  opcache

RUN docker-php-ext-configure gd && \
  docker-php-ext-install gd

RUN docker-php-ext-install \
  intl \
  mbstring \
  xml

# Configure nginx
COPY .deploy/config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY .deploy/config/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY .deploy/config/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configure supervisord
RUN mkdir -p /var/log/supervisor && \
  chmod -R 777 /var/log/supervisor && \
  chown -R nobody:nobody /var/log/supervisor

COPY .deploy/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure files/folders needed by the processes are accessible when they run under the nobody user
RUN chown -R nobody:nobody /var/www/html /run /var/lib/nginx /var/log/nginx

# Switch to use a non-root user from here on
USER nobody

# Add application
COPY --chown=nobody . /var/www/html/

# Expose the port nginx is reachable on
EXPOSE 8080

# Run Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-interaction --no-progress

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

HEALTHCHECK --interval=5s --timeout=10s --start-period=30s --retries=3 \
  CMD curl --silent --fail http://127.0.0.1:8080/api/ping || exit 1
