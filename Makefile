.PHONY: help setup up down restart logs shell test lint migrate seed fresh

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

setup: ## Initial project setup (Modified to run inside Docker)
	@echo "Setting up project..."
	cp -n .env.example .env || true
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app php artisan key:generate
	@echo "Setup complete."

up: ## Start Docker services
	docker-compose up -d
	@echo "Services started. App available at http://localhost:8000"

down: ## Stop Docker services
	docker-compose down

restart: ## Restart Docker services
	docker-compose restart

logs: ## View Docker logs
	docker-compose logs -f

shell: ## Open shell in app container
	docker-compose exec app sh

test: ## Run all tests inside the container
	docker-compose exec app php artisan test

lint: ## Run linters inside the container
	docker-compose exec app ./vendor/bin/pint
	docker-compose exec app npm run lint || true

migrate: ## Run database migrations inside the container (Solves driver issues)
	docker-compose exec app php artisan migrate --force

seed: ## Seed databases inside the container
	docker-compose exec app php artisan db:seed --force

fresh: ## Fresh migration with seed inside the container
	docker-compose exec app php artisan migrate:fresh --seed --force

install-deps: ## Install dependencies inside the container
	docker-compose exec app composer install
	docker-compose exec app npm install

build: ## Build frontend assets inside the container
	docker-compose exec app npm run build
