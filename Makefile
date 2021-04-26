.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

tests:
	vendor/bin/tester tests -s -p php -c tests/php-unix.ini -i
