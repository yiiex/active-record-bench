FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libsqlite3-dev \
        libonig-dev \
        unzip \
        git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
        pdo_sqlite \
        mbstring \
        opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# The mounted repo is owned by the host user, git (run as root) needs this to
# read the branch for phpbench metadata.
RUN git config --global --add safe.directory /app

WORKDIR /app

COPY docker/php/conf.d/zz-bench.ini /usr/local/etc/php/conf.d/zz-bench.ini

CMD ["tail", "-f", "/dev/null"]
