# MyLaravelApp

Base Laravel minimaliste en architecture MVC, Docker Compose, MySQL 8, Nginx, PHP-FPM, Vite et Bootstrap 5.

Le projet expose une seule page `/` rendue par un contrôleur Laravel et une vue Blade :
- title HTML : `MyLaravelApp`
- header visible : `MyLaravelApp`
- contenu : `Hello world`

## Architecture

Le socle utilise 4 conteneurs :
- `app` : PHP 8.3 FPM + Composer
- `nginx` : serveur HTTP
- `node` : installation et build Vite
- `db` : MySQL 8

Ce choix garde une base simple tout en restant cohérent avec `PHP-FPM + Nginx` et `Bootstrap via Vite`.

## Prérequis

- Docker
- Docker Compose

Référence Laravel :
- Installation : https://laravel.com/docs/13.x/installation

## Initialiser le projet sur une nouvelle machine

### 1. Cloner le dépôt

```bash
git clone <repo-url>
cd blog_laravel
```

Référence Laravel :
- Installation : https://laravel.com/docs/13.x/installation

### 2. Construire les images Docker

```bash
make build
```

Cette étape prépare l’environnement `PHP-FPM` utilisé par Laravel.

Référence Laravel :
- Installation : https://laravel.com/docs/13.x/installation

### 3. Initialiser complètement l’application

```bash
make init
```

Cette commande :
- démarre MySQL
- attend que la base soit prête
- installe les dépendances Composer
- crée `.env` si nécessaire
- génère la clé d’application
- lance les migrations
- installe les dépendances frontend
- compile les assets Vite
- démarre tous les conteneurs

Références Laravel :
- Configuration : https://laravel.com/docs/13.x/configuration
- Base de données : https://laravel.com/docs/13.x/database
- Migrations : https://laravel.com/docs/13.x/migrations
- Frontend / Vite : https://laravel.com/docs/13.x/frontend

### 4. Ouvrir l’application

```text
http://localhost:8000
```

Références Laravel :
- Routing : https://laravel.com/docs/13.x/routing
- Controllers : https://laravel.com/docs/13.x/controllers
- Blade : https://laravel.com/docs/13.x/blade

## Démarrage quotidien

```bash
make up
```

Puis, si tu développes le front en live :

```bash
make npm-dev
```

Pour arrêter :

```bash
make down
```

Référence Laravel :
- Frontend / Vite : https://laravel.com/docs/13.x/frontend

## Commandes Make disponibles

```bash
make help
```

Principales commandes :
- `make build` : construit les images Docker
- `make up` : démarre les conteneurs
- `make down` : arrête les conteneurs
- `make init` : initialise un clone neuf
- `make artisan CMD="route:list"` : exécute une commande Artisan
- `make migrate` : lance les migrations
- `make fresh` : recrée la base
- `make test` : lance les tests Laravel
- `make npm-install` : installe les dépendances frontend
- `make npm-dev` : lance Vite en mode dev
- `make npm-build` : compile les assets
- `make logs` : suit les logs Docker

Références Laravel :
- Artisan : https://laravel.com/docs/13.x/artisan
- Testing : https://laravel.com/docs/13.x/testing

## Structure minimale du code

```text
.
├── docker-compose.yml
├── docker/
│   ├── nginx/default.conf
│   └── php/Dockerfile
├── Makefile
├── README.md
└── src/
    ├── app/Http/Controllers/HomeController.php
    ├── resources/views/layouts/app.blade.php
    ├── resources/views/home.blade.php
    ├── routes/web.php
    └── tests/Feature/ExampleTest.php
```

## Flux MVC minimal

### Route

`routes/web.php` déclare la route `/`.

Référence Laravel :
- Routing : https://laravel.com/docs/13.x/routing

### Controller

`HomeController` retourne la vue `home`.

Référence Laravel :
- Controllers : https://laravel.com/docs/13.x/controllers

### View

Le layout Blade contient le header `MyLaravelApp` et la vue `home` affiche `Hello world`.

Référence Laravel :
- Blade : https://laravel.com/docs/13.x/blade

## Bootstrap et assets

Bootstrap est intégré via NPM et Vite, pas via CDN.

Références :
- Laravel Frontend : https://laravel.com/docs/13.x/frontend
- Bootstrap 5.3 : https://getbootstrap.com/docs/5.3/getting-started/introduction/

## Installation sur un serveur Dockerisé simple

Sur un serveur avec Docker et Docker Compose :

```bash
git clone <repo-url>
cd blog_laravel
make build
make init
```

Ensuite, expose le port `8000` ou place un reverse proxy devant Nginx si nécessaire.

Ce README couvre un bootstrap serveur simple, pas un durcissement de production complet.

Référence Laravel :
- Deployment : https://laravel.com/docs/13.x/deployment

## Vérification rapide

```bash
make test
docker compose exec app php artisan route:list
```

Le test Feature vérifie que `/` renvoie `200`, affiche `MyLaravelApp` et `Hello world`.

Référence Laravel :
- HTTP Tests : https://laravel.com/docs/13.x/http-tests
