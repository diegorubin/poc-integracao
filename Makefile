.DEFAULT_GOAL := build

build:
	./vendor/bin/phar-composer build . dist

test:
	./vendor/bin/phpunit tests --coverage-html coverage
