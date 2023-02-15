app-dev:
	cd vue_app/ && yarn dev

api-dev:
	php artisan serve

pint:
	./vendor/bin/pint --test

laratail:
	laratail storage/logs/laravel.log

build-api:
	docker build -t ds_api .

run-api:
	docker run -d -p 8080:8080 ds_api

test:
	./vendor/phpunit/phpunit/phpunit --colors --testdox --exclude-group youtube

tf:
	./vendor/phpunit/phpunit/phpunit --colors --testdox --exclude-group youtube --filter "$0"

optimize:
	php artisan optimize && composer dump-autoload

test-full:
	composer run test

test-migrate:
	php artisan migrate --env testing