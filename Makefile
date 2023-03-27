init:
	# Install composer deps
	composer install

	# Install PHP CS Fixer
	mkdir --parents tools/php-cs-fixer
	composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer

analyse:
	./vendor/bin/phpstan analyse --memory-limit 2G

test:
	./vendor/bin/phpunit

test-coverage:
	./vendor/bin/phpunit --coverage-html=tests/report

check:
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --config .php-cs-fixer.dist.php --format gitlab

format:
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php
