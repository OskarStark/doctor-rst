test:
	bin/phpunit

cs:
	docker run --rm -it -w /app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:2.16.7

phpstan:
	docker run --rm -it -w=/app -v ${PWD}:/app oskarstark/phpstan-ga:0.12.56 analyse -c phpstan.neon.dist

update-rules.md-file:
	php bin/console rules > docs/rules.md

docs-update:
	make update-rules.md-file && git add docs && git commit -m"Update the rules documentation (generated)" && git push

DATE := $(shell date +%Y-%m-%d_%H-%M-%S)
vendor-updates:
	@git checkout -b support/vendor-updates-$(DATE)
	@docker run --rm -it -v ${PWD}:/app composer:1.9.3 bash -c "PHP_MEMORY_LIMIT=2048M php /usr/bin/composer install --no-interaction --no-scripts"
	@docker run --rm -it -v ${PWD}:/app composer:1.9.3 bash -c "PHP_MEMORY_LIMIT=2048M php /usr/bin/composer update --no-scripts --no-ansi --no-progress --no-suggest --no-autoloader 2>&1 | tee commit-msg-raw-$(DATE)"
	@git add composer.lock symfony.lock
	@sed -e"1,7d" -e"/Package operations/G" -e "s/: Loading from cache//g" -e "s/: Downloading (100%)//g" -e"s/Writing lock file//g" commit-msg-raw-$(DATE) > commit-msg-$(DATE)
	@git commit -F commit-msg-$(DATE)
	@rm commit-msg-raw-$(DATE) commit-msg-$(DATE)
	@git push --set-upstream origin support/vendor-updates-$(DATE)
