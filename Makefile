.DEFAULT_GOAL := build

build:
	rm -rf dist/integracao
	./phar-composer-1.2.0.phar build . dist/integracao

build-image:
	docker build -t docker.io/diegorubin/tex-integracao:0.3 .

push-image:
	docker push docker.io/diegorubin/tex-integracao:0.3

test:
	./vendor/bin/phpunit tests --coverage-html coverage
