APP_SERVICE = docker compose exec php-fpm

env:
	cp -n .env.example .env

keys:
	$(APP_SERVICE) php artisan jwt:secret
	$(APP_SERVICE) php artisan key:generate

test_keys:
	$(APP_SERVICE) php artisan --env=testing key:generate
	$(APP_SERVICE) php artisan --env=testing jwt:secret

dep_install:
	$(APP_SERVICE) composer install

install: env
	docker compose up --build -d

setup: install dep_install keys test_keys dev_migration test_migration
	docker compose stop

dev_migration:
	$(APP_SERVICE) php artisan migrate --seed

test_migration:
	$(APP_SERVICE) php artisan --env=testing migrate --seed

recreate_test_db:
	$(APP_SERVICE) php artisan --env=testing migrate:refresh --seed

recreate_dev_db:
	$(APP_SERVICE) php artisan migrate:refresh --seed

up:
	docker compose up -d

.PHONY: tests

tests:
	$(APP_SERVICE) php artisan test --env=testing
