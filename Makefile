.DEFAULT_GOAL := build

build:
	rm -rf dist/poc-integracao.phar
	./vendor/bin/phar-composer build . dist

test:
	./vendor/bin/phpunit tests --coverage-html coverage
