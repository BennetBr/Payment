FROM php:8-fpm-alpine
LABEL Name=payment Version=1.0.0
RUN apk update; \
    apk upgrade; \
    apk --no-cache add ${PHPIZE_DEPS};

#Install additional PHP modules
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql;

#Install & enable XDebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug;

# Use the default development configuration
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini";

#configure xdebug
RUN echo 'xdebug.remote_port=9000' >> "$PHP_INI_DIR/php.ini"; \
    echo 'xdebug.remote_enable=1' >> "$PHP_INI_DIR/php.ini"; \
    echo 'xdebug.remote_autostart=1' >> "$PHP_INI_DIR/php.ini"; \
    echo 'xdebug.remote_connect_back=1' >> "$PHP_INI_DIR/php.ini"