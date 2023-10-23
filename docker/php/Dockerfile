FROM php:8.2-fpm-alpine

ARG WWWUSER
ARG WWWGROUP

ARG XDEBUG_IDE_KEY="PHPSTORM"
ARG XDEBUG_HOST="host.docker.internal"

RUN apk update
RUN apk add --update linux-headers
RUN apk add bash openssh libzip

RUN apk add --no-cache $PHPIZE_DEPS

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
#RUN docker-php-ext-install pdo_sqlite

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN pecl install -o -f redis &&  docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a group and user
RUN addgroup -g $WWWGROUP -S www
RUN adduser -S --uid $WWWUSER -G www www

# Tell docker that all future commands should run as the appuser user
USER appuser

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]
