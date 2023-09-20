.PHONY: test
test:
	vendor/bin/phpunit

.PHONY: cs
cs: vendor
	symfony php vendor/bin/php-cs-fixer fix --diff --verbose

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon.dist

.PHONY: phpstan-baseline
phpstan-baseline:
	vendor/bin/phpstan analyse -c phpstan.neon.dist --generate-baseline=phpstan-baseline.neon

.PHONY: refactoring
refactoring:
	vendor/bin/rector process --config rector.php

.PHONY: dependency-analysis
dependency-analysis: vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	symfony php tools/composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json
	vendor/bin/composer-unused
