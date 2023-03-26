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
