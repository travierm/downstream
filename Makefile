app-dev:
	cd vue_app/ && yarn dev

api-dev:
	php artisan serve

laratail:
	laratail storage/logs/laravel.log