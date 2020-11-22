test:
	vendor/bin/phpunit

cs:
	docker run --rm -it -w /app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:2.16.7

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon.dist
