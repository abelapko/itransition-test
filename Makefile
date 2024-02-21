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