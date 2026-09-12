#!make
include .env

sail-init:
	docker run --rm \
        -v .:/var/www/html \
        -w /var/www/html \
        vigorexa/laravel-sail-core:php85-alpine-slim-latest \
        composer install --ignore-platform-reqs

pint:
	./vendor/bin/pint --config ${PINT_CONFIG}

