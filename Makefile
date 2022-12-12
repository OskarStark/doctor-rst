test:
	vendor/bin/phpunit

cs:
	docker run --rm -it -w /app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:3.0.0

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon.dist

phpstan-baseline:
	vendor/bin/phpstan analyse -c phpstan.neon.dist --generate-baseline=phpstan-baseline.neon
