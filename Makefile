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


service-deploy:
	./sh-process-env.sh ./docker/$(SERVICE)/.env.example .env > ./docker/$(SERVICE)/.env
	./sh-process-compose-file.sh ./docker/$(SERVICE)/docker-compose.$(SERVICE).$(TARGET).yml > ./docker/$(SERVICE)/docker-compose.$(SERVICE).$(TARGET).processed.yml
	cd ./docker/$(SERVICE); docker stack deploy -d -c docker-compose.$(SERVICE).$(TARGET).processed.yml app_$(SERVICE)

debug-stack-deploy:
	./sh-process-env.sh --fill-missing .env.develop > .env

	$(MAKE) service-deploy SERVICE=traefik TARGET=develop
	$(MAKE) service-deploy SERVICE=rustfs TARGET=develop
	$(MAKE) service-deploy SERVICE=pgsql TARGET=develop
	$(MAKE) service-deploy SERVICE=mailpit TARGET=develop
	$(MAKE) service-deploy SERVICE=keydb TARGET=develop

	./sh-process-compose-file.sh docker-compose.develop.yml > docker-compose.processed.yml
	docker stack deploy -d -c docker-compose.processed.yml app_laravel

debug-stack-rm:
	docker stack rm app_laravel app_rustfs app_pgsql app_mailpit app_keydb app_traefik


create-bucket:
	docker run --rm \
		-e AWS_ENDPOINT="$(AWS_ENDPOINT)" \
		-e AWS_ACCESS_KEY_ID="$(AWS_ACCESS_KEY_ID)" \
		-e AWS_SECRET_ACCESS_KEY="$(AWS_SECRET_ACCESS_KEY)" \
		--entrypoint /bin/sh \
		rustfs/rc:latest \
		-ec ' \
			rc alias set local "$$AWS_ENDPOINT" "$$AWS_ACCESS_KEY_ID" "$$AWS_SECRET_ACCESS_KEY"; \
			rc bucket create local/laravel --ignore-existing \
		'
