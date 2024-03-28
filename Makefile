init:
	# Install composer deps
	composer install

	# Install PHP CS Fixer
	mkdir --parents tools/php-cs-fixer
	composer require --dev --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer

analyse:
	./vendor/bin/phpstan analyse --memory-limit 2G

test:
	./vendor/bin/phpunit

test_coverage:
	./vendor/bin/phpunit --coverage-html=tests/report

check:
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --config .php-cs-fixer.dist.php --format gitlab

format:
	PHP_CS_FIXER_IGNORE_ENV=1 tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php
