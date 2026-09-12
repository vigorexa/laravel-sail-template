FROM vigorexa/laravel-sail-core:php85-alpine-slim-latest AS base


FROM base AS frontend

COPY ./package.json ./package.json
COPY ./package-lock.json ./package-lock.json

RUN npm ci

COPY ./vite.config.js ./vite.config.js
COPY ./resources ./resources
COPY --parents ./app-modules/*/resources ./

RUN npm run build


FROM base AS copy-composers

COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock
COPY --parents ./app-modules/*/composer.json ./


FROM copy-composers AS production

RUN composer install --no-dev --optimize-autoloader --no-scripts --ansi

COPY ./ ./

COPY --from=frontend /var/www/html/public/build ./public/build

RUN rm -rf tests phpunit.xml

RUN composer dump-autoload --optimize --ansi \
 && php artisan package:discover --ansi \
 && php artisan filament:assets \
 && php artisan storage:link

ENTRYPOINT ["/bin/sh", "-ec", "php artisan optimize --no-interaction && exec /sbin/tini -s -- \"$@\"", "--"]


FROM copy-composers AS development

RUN composer install --no-scripts --ansi

COPY ./ ./

COPY --from=frontend /var/www/html/public/build ./public/build

RUN composer dump-autoload --ansi \
 && php artisan package:discover --ansi \
 && php artisan filament:assets \
 && php artisan storage:link
