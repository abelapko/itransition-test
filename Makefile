init: init-config init-app
restart: down up

init-app:
	docker-compose run --rm php composer install

init-config:
	cp .env.tpl .env

up:
	docker-compose up -d

down:
	docker-compose down

down-v:
	docker-compose down -v

test:
	docker-compose run --rm php composer run tests

cli:
	docker-compose run --rm php bash

lint:
	docker-compose run --rm php composer run cs-check

lint-fix:
	docker-compose run --rm php composer run cs-fix

migrate-up: migrate-up-app migrate-up-tests

migrate-down: migrate-down-app migrate-down-tests

migrate-reset: migrate-down migrate-up

migrate-up-app:
	docker-compose run --rm php yii migrate --interactive=0

migrate-up-tests:
	docker-compose run --rm php tests/bin/yii migrate --interactive=0

migrate-down-app:
	docker-compose run --rm php yii migrate/fresh --interactive=0

migrate-down-tests:
	docker-compose run --rm php tests/bin/yii migrate/fresh --interactive=0