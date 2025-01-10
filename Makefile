APP_SERVICE = docker compose run --rm php-fpm

build:
	cp -n .env.example .env
	docker compose build
	$(APP_SERVICE) composer install

install: build prepare

prepare: prepare_tests
	@$(APP_SERVICE) php artisan jwt:secret
	@$(APP_SERVICE) php artisan key:generate
	@$(APP_SERVICE) php artisan migrate --seed

prepare_tests:
	$(APP_SERVICE) php artisan --env=testing key:generate
	$(APP_SERVICE) php artisan --env=testing jwt:secret
	$(APP_SERVICE) php artisan --env=testing migrate --seed

recreate_test_tables:
	$(APP_SERVICE) php artisan --env=testing migrate:refresh --seed

recreate_dev_tables:
	$(APP_SERVICE) php artisan migrate:refresh --seed

up:
	docker compose up -d

.PHONY: tests

tests:
	$(APP_SERVICE) php artisan test --env=testing
