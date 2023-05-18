FROM php:8-cli-alpine
LABEL Name=payment-tests Version=1.0.0
RUN apk update; \
    apk upgrade; \
    apk --no-cache add composer php-dom php-xml php-xmlwriter php-tokenizer;

#Install additional PHP modules
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql;

# Use the default development configuration
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini";

WORKDIR /var/www/html/
CMD composer upgrade && php /host_shared_cache/vendor/bin/phpunit --cache-result-file /host_shared_cache/.phpunit.result.cache
