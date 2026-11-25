# Gente Con Talento - Docker Environment (SQLite)
.PHONY: help dev down restart logs app-shell migrate test install assets watch clean status

# Default target
help: ## Show this help message
	@echo "Gente Con Talento - Docker Commands"
	@echo "==================================="
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Application Commands
dev: ## Start the full stack in development (live source + OPcache off)
	docker compose up -d --build
	@echo "App running at http://localhost:8080 (Vite HMR on 5174)."
	@echo "Build frontend with 'make assets' (or 'make watch'). Run tests with 'make test'."

down: ## Stop all containers
	docker compose down

restart: ## Restart containers
	docker compose restart

logs: ## Show application and web server logs
	docker compose logs -f

app-shell: ## Access the application container shell
	docker compose exec app sh

migrate: ## Run database migrations inside the app container
	docker compose exec app php artisan migrate --force

test: ## Run the test suite (usage: make test [args="--filter=ExampleTest"])
	docker compose exec \
		-e APP_ENV=testing \
		-e DB_CONNECTION=sqlite \
		-e DB_DATABASE=:memory: \
		app php artisan test $(args)

install: ## Install PHP + frontend dependencies inside the app container
	docker compose exec app composer install
	docker compose exec app npm install
	@echo "Dependencies installed."

assets: ## Build frontend assets once (inside the app container)
	docker compose exec app rm -f public/hot
	docker compose exec app npm run build
	@echo "Frontend assets rebuilt."

watch: ## Rebuild frontend assets on every change (inside the app container)
	docker compose exec app npm run dev -- --host 0.0.0.0 --port 5174

clean: ## Clean up Docker resources
	docker compose down -v
	docker system prune -f
	@echo "Docker resources cleaned"

status: ## Show container status
	docker compose ps
