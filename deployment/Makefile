#!make
include .env


stack-service-deploy:
	./sh-process-env.sh ./$(SERVICE)/.env.example .env > ./$(SERVICE)/.env
	./sh-process-compose-file.sh ./$(SERVICE)/docker-compose.$(SERVICE).yml > ./$(SERVICE)/docker-compose.$(SERVICE).processed.yml
	cd ./$(SERVICE); docker stack deploy -d -c docker-compose.$(SERVICE).processed.yml app_$(SERVICE)

stack-full-deploy:
	$(MAKE) stack-service-deploy SERVICE=traefik
	$(MAKE) stack-service-deploy SERVICE=monitoring
	$(MAKE) stack-service-deploy SERVICE=rustfs
	$(MAKE) stack-service-deploy SERVICE=pgsql
	$(MAKE) stack-service-deploy SERVICE=mailpit
	$(MAKE) stack-service-deploy SERVICE=keydb
	$(MAKE) stack-service-deploy SERVICE=laravel

stack-full-rm:
	docker stack rm app_laravel app_rustfs app_pgsql app_mailpit app_keydb app_monitoring app_traefik


rustfs-create-bucket:
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


grafana-dashboards-import:
	@for f in monitoring/dashboards/*.json; do \
		curl -sf -u "$(GF_SECURITY_ADMIN_USER):$(GF_SECURITY_ADMIN_PASSWORD)" \
			-H "Content-Type: application/json" \
			-X POST "http://$(GRAFANA_HOST)/api/dashboards/db" \
			-d "$$(jq -n --slurpfile dashboard "$$f" '{dashboard: $$dashboard[0], overwrite: true}')"; \
	done
