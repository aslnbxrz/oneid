.PHONY: help install update qa lint format analyse test test-coverage test-unit test-feature clean hooks release bump-patch bump-minor bump-major ci

PHP       ?= php
COMPOSER  ?= composer
PINT      := ./vendor/bin/pint
PEST      := ./vendor/bin/pest

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  %-18s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	$(COMPOSER) install --prefer-dist --no-progress

update: ## Update dependencies with all deps resolution
	$(COMPOSER) update -W --prefer-dist --no-progress

qa: ## Validate + Lint(test) + Tests
	$(COMPOSER) validate --strict
	$(PINT) --test
	$(PEST)

lint: ## Lint (non-destructive)
	$(PINT) --test

format: ## Format code (destructive)
	$(PINT)

analyse: ## Static analysis (optional)
	$(COMPOSER) analyse || true

test: ## Run tests
	$(PEST)

test-coverage: ## Run tests with coverage
	$(COMPOSER) test:coverage

test-unit: ## Run unit tests only
	$(COMPOSER) test:unit

test-feature: ## Run feature tests only
	$(COMPOSER) test:feature

clean: ## Clean vendor and lock
	rm -rf vendor/ composer.lock

hooks: ## Install git hooks (pre-push runs auto-fix + validate + lint + tests)
	chmod +x scripts/git-hooks/pre-push
	git config core.hooksPath scripts/git-hooks
	@echo "Git hooks installed."

ci: ## Local CI: update + validate + lint + tests
	$(COMPOSER) update -W --prefer-dist --no-progress
	$(COMPOSER) validate --strict
	$(PINT) --test
	$(PEST)

bump-patch: ## Bump patch (QA + tag + push)
	./scripts/bump-version.sh patch --auto-fix

bump-minor: ## Bump minor (QA + tag + push)
	./scripts/bump-version.sh minor --auto-fix

bump-major: ## Bump major (QA + tag + push)
	./scripts/bump-version.sh major --auto-fix

release: ## Quick patch release with auto-fix
	./scripts/bump-version.sh patch --auto-fix