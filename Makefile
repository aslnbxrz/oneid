.PHONY: help install test test-coverage lint format analyse clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	composer install

test: ## Run tests
	composer test

test-coverage: ## Run tests with coverage
	composer test:coverage

test-unit: ## Run unit tests only
	composer test:unit

test-feature: ## Run feature tests only
	composer test:feature

lint: ## Run code style checks
	composer lint

lint-test: ## Test code style without fixing
	composer lint:test

format: ## Format code
	composer format

analyse: ## Run static analysis
	composer analyse

clean: ## Clean up generated files
	rm -rf vendor/
	rm -rf composer.lock

bump-patch: ## Bump patch version
	./scripts/bump-version.sh patch

bump-minor: ## Bump minor version
	./scripts/bump-version.sh minor

bump-major: ## Bump major version
	./scripts/bump-version.sh major

release: ## Run tests and bump patch version
	composer test && make bump-patch

setup: install ## Setup development environment
	@echo "Development environment setup complete!"
	@echo "Run 'make test' to verify everything is working."

ci: lint test ## Run CI pipeline (lint + test)