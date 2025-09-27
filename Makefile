.PHONY: help install update qa lint format analyse test test-coverage test-unit test-feature clean hooks release bump-patch bump-minor bump-major ci

# Binaries (override locally if needed, e.g., make test PHP=/path/to/php)
PHP       ?= php
COMPOSER  ?= composer
BIN       := vendor/bin

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  %-18s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies (library repos often skip lock)
	$(COMPOSER) install --prefer-dist --no-progress

update: ## Update dependencies with all deps resolution
	$(COMPOSER) update -W --prefer-dist --no-progress

qa: lint test ## Run lint + tests (quick quality gate)

lint: ## Run code style checks (non-destructive)
	$(COMPOSER) lint:test

format: ## Format code (destructive)
	$(COMPOSER) format

analyse: ## Run static analysis (if configured)
	$(COMPOSER) analyse || true

test: ## Run tests
	$(COMPOSER) test

test-coverage: ## Run tests with coverage (if configured)
	$(COMPOSER) test:coverage

test-unit: ## Run unit tests only
	$(COMPOSER) test:unit

test-feature: ## Run feature tests only
	$(COMPOSER) test:feature

clean: ## Clean vendor and lock
	rm -rf vendor/ composer.lock

hooks: ## Install git hooks (pre-push runs lint+tests)
	chmod +x scripts/git-hooks/pre-push
	git config core.hooksPath scripts/git-hooks
	@echo "Git hooks installed."

ci: ## Run CI-equivalent locally (update deps + lint + tests)
	$(COMPOSER) update -W --prefer-dist --no-progress
	$(COMPOSER) lint:test
	$(COMPOSER) test

# Release helpers (all of them run qa gate inside the script)
bump-patch: ## Bump patch (auto-fix workspace)
	./scripts/bump-version.sh patch --auto-fix

bump-minor: ## Bump minor (auto-fix workspace)
	./scripts/bump-version.sh minor --auto-fix

bump-major: ## Bump major (auto-fix workspace)
	./scripts/bump-version.sh major --auto-fix

release: ## Quick patch release with auto-fix
	./scripts/bump-version.sh patch --auto-fix