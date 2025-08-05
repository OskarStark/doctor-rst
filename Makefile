.PHONY: tests-changed
tests-changed: export APP_ENV=test
tests-changed: vendor doctrine
	symfony php vendor/bin/phpunit --configuration=phpunit.xml.dist $(shell git diff HEAD --name-only | grep Test.php | xargs )

.PHONY: tests
tests: export APP_ENV=test
tests:
	vendor/bin/phpunit

.PHONY: cs
cs: vendor
	symfony php vendor/bin/php-cs-fixer fix --diff --verbose

.PHONY: static-code-analysis
static-code-analysis:
	vendor/bin/phpstan analyse -c phpstan.neon.dist --memory-limit=-1

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline:
	vendor/bin/phpstan analyse -c phpstan.neon.dist --generate-baseline=phpstan-baseline.neon --memory-limit=1G

.PHONY: refactoring
refactoring:
	vendor/bin/rector process --config rector.php

.PHONY: dependency-analysis
dependency-analysis: vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	symfony php vendor/bin/composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json
	symfony php vendor/bin/composer-unused
	symfony composer audit

.PHONY: docs
docs: vendor
	symfony php bin/doctor-rst rules > docs/rules.md
