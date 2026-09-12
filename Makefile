#!make
include .env

COMPOSE_PROJECT_NAME ?= $(shell basename $(CURDIR))

sail-init:
	docker run --rm \
        -v .:/var/www/html \
        -w /var/www/html \
        vigorexa/laravel-sail-core:php85-alpine-slim-latest \
        composer install --ignore-platform-reqs

pint:
	./vendor/bin/pint --config ${PINT_CONFIG}

rustfs-create-bucket:
	docker run --rm \
		--network $(COMPOSE_PROJECT_NAME)_application \
		-e AWS_ENDPOINT=http://rustfs:9000 \
		-e AWS_ACCESS_KEY_ID=$(AWS_ACCESS_KEY_ID) \
		-e AWS_SECRET_ACCESS_KEY=$(AWS_SECRET_ACCESS_KEY) \
		--entrypoint /bin/sh \
		rustfs/rc:latest \
		-ec ' \
			rc alias set local "$$AWS_ENDPOINT" "$$AWS_ACCESS_KEY_ID" "$$AWS_SECRET_ACCESS_KEY"; \
			rc bucket create local/$(AWS_BUCKET) --ignore-existing \
		'

