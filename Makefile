.DEFAULT_GOAL := help

export UID := $(shell id -u)
export ENV ?= dev
export DEBUG = 1
export DOCKER_PHP_PORT = 9000
export DOCKER_MYSQL_PORT = 3306
export PHP_DATE_TIMEZONE = UTC
export DATABASE_URL = mysql://root@mysql/sylius_plugin_$(ENV)

DOCKER_OPTIONS = -u $(UID)
DOCKER_UP_OPTIONS = --remove-orphans -d
DOCKER_CMD = docker-compose
SUB_MAKE := $(shell which make)
DOCKER_COMPOSE_RUN = $(DOCKER_CMD) run --rm
NPM_CMD = $(DOCKER_CMD) run node npm
PHP_CMD = $(DOCKER_CMD) exec $(DOCKER_OPTIONS) php
PHP_CLI_CMD = $(DOCKER_CMD) run php-cli
CONSOLE_CMD = $(PHP_CMD) bin/console
BUILD_CMD = $(NPM_CMD) run build
COMPOSER_CMD = $(DOCKER_COMPOSE_RUN) composer composer
COMPOSER_CMD_INSTALL = $(COMPOSER_CMD) install

ifeq ($(ENV),ci)
	NPM_CMD = npm --prefix tests/Application/
	PHP_CMD = php
	COMPOSER_CMD = composer
	COMPOSER_CMD_INSTALL = $(COMPOSER_CMD) install --no-scripts
	NPM_CMD_INSTALL = $(NPM_CMD) ci --ignore-script
endif

.PHONY: help
help:
	@grep -E '(^[ \/%a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## Project setup
## ENV = dev (default) | production | ci
##---------------------------------------------------------------

.PHONY: start
start: pull docker-compose.yaml ## Build docker images and up containers
	@$(DOCKER_CMD) up $(DOCKER_UP_OPTIONS)

.PHONY: pull
pull:
	@$(DOCKER_CMD) pull --quiet

.PHONY: stop
stop: ## Stop docker containers
	@$(DOCKER_CMD) stop

.PHONY: fixtures
fixtures: ## Install fixtures
	@$(CONSOLE_CMD) sylius:fixtures:load

.PHONY: assets
assets: ## Install assets
	@$(CONSOLE_CMD) assets:install public

.PHONY: build
build: tests/Application/node_modules ## Build javascript / scss
	@$(BUILD_CMD)

##
## Developpment
##---------------------------------------------------------------

.PHONY: cache-clear
cache-clear: ## Clear symfony cache
	@$(CONSOLE_CMD) cache:clear --env=$(ENV)

.PHONY: logs-node
logs-node: ## Display logs node
	@$(DOCKER_CMD) logs -f node

.PHONY: logs-php
logs-php: ## Display logs php
	@$(DOCKER_CMD) logs -f php

.PHONY: logs-composer
logs-composer: ## Display logs composer
	@$(DOCKER_CMD) logs -f composer

tests/Application/node_modules:
	@$(NPM_CMD) install


.PHONY: clean/% # Clean a file or a folder in the project like `make clean/vendor` for example
clean/%:
	rm -rf ./$*

.PHONY: vendor/autoload.php
vendor/autoload.php: composer.json
	@$(COMPOSER_CMD_INSTALL)

install: .install ## Install project and it may not reinstall if it's already done
	> $<

.install: vendor/autoload.php

reinstall: clean/.install clean/vendor ## Reinstall vendor
	$(SUB_MAKE) vendor/autoload.php

##
## Migrations
##----------------------------------------------------------------------------

.PHONY: db-validate
db-validate: ## Checks and validate mapping informations
	@$(PHP_CMD) bin/console doctrine:schema:validate

.PHONY: db-update
db-update: ## Sync database with schema
	@$(PHP_CMD) bin/console doctrine:schema:update --force

.PHONY: db-create
db-create: ## Create database
	@$(PHP_CMD) bin/console doctrine:database:create

.PHONY: db-drop
db-drop: ## Checks and validate mapping informations
	@$(PHP_CMD) bin/console doctrine:database:drop --force

##
## Symfony
##----------------------------------------------------------------------------

.PHONY: debug-router
debug-router: ## Displays routing informations
	@$(PHP_CMD) bin/console debug:router

.PHONY: debug-container
debug-container: ## Displays services informations
	@$(PHP_CMD) bin/console debug:container

##
## Test
##----------------------------------------------------------------------------
.PHONY: phpspec-run
phpspec-run: vendor/autoload.php ## Runs PHPSpec test suite for the optional given class. Ex : make phpspec-run -> Runs all test suite (App only) OR make phpspec-run CLASS=spec/Service/MyServiceSpec.php
	@$(COMPOSER_CMD) run-script test:phpspec $(CLASS)

.PHONY: test-spec
test-spec: phpspec-run ## Execute specification tests

##
## ECS
##----------------------------------------------------------------------------
.PHONY: back-test-cs
back-test-cs: vendor/autoload.php ## Execute tests of coding style for backend
	@$(COMPOSER_CMD) run-script test:ecs

.PHONY: fix-back-test-cs
fix-back-test-cs: vendor/autoload.php ## Execute tests of coding style for backend with fixes active. WARNING : review changes before commit !
	@$(PHP_CLI_CMD) vendor/bin/ecs check src tests/Application --fix

.PHONY: test-cs
test-cs: front-test-cs back-test-cs ## Execute tests of coding style

.PHONY: back-test-stan
back-test-stan: vendor/autoload.php
	$(PHP_CMD) vendor/bin/phpstan analyse src -c phpstan.neon --level=4

.PHONY: front-test-cs
front-test-cs: tests/Application/node_modules ## Execute tests of coding style for frontend
	@$(NPM_CMD) run lint

.PHONY: test-cs
test-cs: front-test-cs back-test-ecs back-test-stan ## Execute tests of coding style
