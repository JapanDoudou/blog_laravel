.DEFAULT_GOAL := help

DOCKER_COMPOSE := docker compose
APP := $(DOCKER_COMPOSE) exec app
APP_RUN := $(DOCKER_COMPOSE) run --rm app
NODE := $(DOCKER_COMPOSE) run --rm node
DB := $(DOCKER_COMPOSE) exec db

help: ## Affiche la liste des commandes disponibles et leur rôle.
	@awk 'BEGIN {FS = ":.*## "}; /^[a-zA-Z0-9_-]+:.*## / {printf "\033[36m%-16s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Construit les images Docker du projet.
	$(DOCKER_COMPOSE) build

up: ## Démarre les conteneurs en arrière-plan.
	$(DOCKER_COMPOSE) up -d

down: ## Arrête et supprime les conteneurs du projet.
	$(DOCKER_COMPOSE) down

init: ## Initialise un clone neuf du projet (dépendances, .env, clé, migrations, front).
	$(DOCKER_COMPOSE) up -d db
	@until $(DB) mysqladmin ping -h 127.0.0.1 -uroot -proot --silent; do sleep 2; done
	$(APP_RUN) composer install
	@if [ ! -f src/.env ]; then cp src/.env.example src/.env; fi
	$(DOCKER_COMPOSE) up -d app
	$(APP) php artisan key:generate
	$(APP) php artisan migrate --force
	$(NODE) npm install
	$(NODE) npm run build
	$(DOCKER_COMPOSE) up -d

artisan: ## Exécute une commande Artisan. Usage: make artisan CMD="route:list"
	$(APP) php artisan $(CMD)

migrate: ## Lance les migrations Laravel.
	$(APP) php artisan migrate

fresh: ## Recrée entièrement la base avec les migrations fraîches.
	$(APP) php artisan migrate:fresh

test: ## Lance la suite de tests Laravel.
	$(APP) php artisan test

npm-install: ## Installe les dépendances frontend.
	$(NODE) npm install

npm-dev: ## Lance Vite en mode développement.
	$(DOCKER_COMPOSE) run --rm --service-ports node npm run dev -- --host 0.0.0.0

npm-build: ## Compile les assets frontend pour la production.
	$(NODE) npm run build

logs: ## Affiche les logs des conteneurs.
	$(DOCKER_COMPOSE) logs -f
