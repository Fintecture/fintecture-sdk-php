init:
	# Install composer deps
	composer install

	# Install PHP CS Fixer
	mkdir --parents tools/php-cs-fixer
	composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
	
	# Define git hooks directory for using precommit hook to automatically format PHP code using PHP CS Fixer:
	git config core.hooksPath .githooks

analyse:
	./vendor/bin/phpstan analyse

test:
	./vendor/bin/phpunit

test-coverage:
	./vendor/bin/phpunit --coverage-html=tests/report

format:
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php
