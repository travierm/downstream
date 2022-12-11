app-dev:
	cd vue_app/ && yarn dev

api-dev:
	php artisan serve

pint:
	./vendor/bin/pint --test

laratail:
	laratail storage/logs/laravel.log