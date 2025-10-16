# Must install composer dependencies before building
# And run `pnpm run build` to generate the assets

FROM dunglas/frankenphp AS base

RUN install-php-extensions \
    pdo_mysql \
    intl \
    zip \
    opcache \
    pcntl \
    gmp

ENV SERVER_NAME=:80

FROM base AS production

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY . /app

ENTRYPOINT [ "./entrypoint.sh" ]
