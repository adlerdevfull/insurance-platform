FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    git \
    curl \
    unzip \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    rabbitmq-c-dev \
    linux-headers \
    autoconf \
    gcc \
    g++ \
    make \
    && docker-php-ext-install pdo pdo_mysql mbstring xml opcache sockets \
    && pecl install amqp redis \
    && docker-php-ext-enable amqp redis \
    && apk del autoconf gcc g++ make

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
