APP_SERVICE = docker compose exec app

deps: build
	$(APP_SERVICE) composer install

build:
	docker compose build

install: deps prepare

prepare:
	cp -n .env.example .env
	$(APP_SERVICE) php artisan jwt:secret
	$(APP_SERVICE) php artisan key:generate
	$(APP_SERVICE) php artisan migrate

up:
	docker compose up -d

prepare_tests:
	$(APP_SERVICE) php artisan --env=testing key:generate
	$(APP_SERVICE) php artisan --env=testing migrate --seed

.PHONY: tests

tests:
	$(APP_SERVICE) php artisan test --env=testing

recreate_test_db:
	$(APP_SERVICE) php artisan --env=testing migrate:refresh --seed
