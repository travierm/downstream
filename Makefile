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