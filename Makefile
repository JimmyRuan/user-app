# Makefile for managing Docker containers and Laravel application

# Load .env variables
ifneq (,$(wildcard ./.env))
    include .env
    export
endif

# Variables
DOCKER_COMPOSE = docker-compose
APP_CONTAINER = $(DOCKER_APP_CONTAINER_NAME)
MYSQL_CONTAINER = $(DOCKER_MYSQL_CONTAINER_NAME)
MYSQL_USER = $(DB_USERNAME)
MYSQL_PASSWORD = $(DB_PASSWORD)
MYSQL_DATABASE = $(DB_DATABASE)

# images for app
IMAGE_NAME=jimmyruan/radium-app
TAG=latest

# Command to access the running "app" container
.PHONY: app-shell
app-shell:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) /bin/bash

# Commands to run all containers and remove orphan containers
.PHONY: up
up:
	$(DOCKER_COMPOSE) up -d --remove-orphans


# Commands to rebuild all containers
.PHONY: rebuild
rebuild:
	$(DOCKER_COMPOSE) up -d --build

# Commands to stop containers
.PHONY: stop
stop:
	$(DOCKER_COMPOSE) stop

.PHONY: down
down:
	$(DOCKER_COMPOSE) down

.PHONY: restart
restart:
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) up -d --build

.PHONY: logs
logs:
	$(DOCKER_COMPOSE) logs -f

# Command to access Laravel Tinker
.PHONY: tinker
tinker:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan tinker

# Command to access MySQL database
.PHONY: db-shell
db-shell:
	$(DOCKER_COMPOSE) exec $(MYSQL_CONTAINER) mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE)

# Command to serve Laravel app in local dev setup
.PHONY: serve
serve:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan serve --host=0.0.0.0 --port=8000

# Command to install Composer dependencies
.PHONY: composer-install
composer-install:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) composer install

# Command to run Laravel migrations
.PHONY: migrate
migrate:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate


# Command to run Laravel migrations
.PHONY: refresh-migration-with-seeding
refresh-migration-with-seeding:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate:fresh --seed

# Command to run all the tests
.PHONY: test
test:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan test

# Command to follow logs for the app container
.PHONY: logs-app
logs-app:
	$(DOCKER_COMPOSE) logs -f app

# build laravel app
build-app:
	docker build --platform linux/amd64 --no-cache -t $(IMAGE_NAME):$(TAG) .

# push laravel app to docker cloud
push-app:
	docker push $(IMAGE_NAME):$(TAG)

# build production laravel app
build-prod-app:
	docker build --platform linux/amd64 --no-cache -f docker/prod/Dockerfile -t jimmyruan/radium-app:production .

# push production laravel app to docker cloud
push-prod-app:
	docker push jimmyruan/radium-app:production

prod-up:
	docker compose -f docker-compose.prod.yml up --remove-orphans --pull always -d

prod-stop:
	docker compose -f docker-compose.prod.yml stop

PROD_DOCKER_COMMAND=docker compose -f docker-compose.prod.yml exec app bash -c

prod-deploy:
	$(PROD_DOCKER_COMMAND) 'composer install --no-dev --optimize-autoloader'
	$(PROD_DOCKER_COMMAND) 'php artisan cache:clear'
	$(PROD_DOCKER_COMMAND) 'php artisan config:clear'
	$(PROD_DOCKER_COMMAND) 'php artisan route:clear'
	$(PROD_DOCKER_COMMAND) 'php artisan view:clear'
	$(PROD_DOCKER_COMMAND) 'php artisan config:cache'
	$(PROD_DOCKER_COMMAND) 'php artisan route:cache'
	$(PROD_DOCKER_COMMAND) 'php artisan view:cache'
	$(PROD_DOCKER_COMMAND) 'php artisan migrate --force'

reset-prod-db:
	$(PROD_DOCKER_COMMAND) 'php artisan migrate:fresh --seed'
