# syntax=docker/dockerfile:1
ARG PHP_VERSION=8.5

# Shared base: install the PHP extensions once so the vendor, frontend, and prod
# stages reuse a single layer instead of compiling ICU/zip/gd three times (which
# was tripling build-time disk usage).
FROM dunglas/frankenphp:php${PHP_VERSION} AS base
RUN install-php-extensions pcntl pdo_pgsql redis intl zip opcache bcmath gd
WORKDIR /app

# Composer Dependencies
FROM base AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --optimize --no-dev   # runs package:discover with full app present

# Frontend assets (needs PHP for wayfinder:generate, plus Node)
FROM base AS frontend
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*
ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
# Vite inlines VITE_* vars into the JS bundle at build time; the runtime .env
# cannot change them afterwards. Must be present before `npm run build`.
ARG VITE_APP_NAME="Martori Farms"
ENV VITE_APP_NAME=${VITE_APP_NAME}
COPY --from=vendor /app/vendor ./vendor
COPY . .
RUN npm ci && npm run build

# Runtime — extensions and curl come from the shared base, so no reinstall here.
FROM base AS prod
# Opcache tuned for long-running Octane workers
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.jit=tracing"; \
      echo "opcache.jit_buffer_size=64M"; \
      echo "opcache.validate_timestamps=0"; \
      echo "opcache.memory_consumption=256"; \
      echo "opcache.max_accelerated_files=20000"; \
    } > /usr/local/etc/php/conf.d/zz-opcache.ini
# Deployed commit SHA, surfaced in-app via config('app.commit').
ARG GIT_COMMIT=unknown
ENV GIT_COMMIT=${GIT_COMMIT}
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
        storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
EXPOSE 80
HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=5 \
    CMD curl -fsS http://localhost/up || exit 1
# Base image ENTRYPOINT (docker-php-entrypoint) execs this CMD; compose overrides it for queue/scheduler
CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=80", "--admin-port=2019"]
