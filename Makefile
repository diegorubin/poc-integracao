.DEFAULT_GOAL := build

build:
	rm -rf dist/poc-integracao.phar
	./phar-composer-1.2.0.phar build . dist/integracao

test:
	./vendor/bin/phpunit tests --coverage-html coverage
