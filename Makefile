app-dev:
	yarn serve

api-dev:
	php artisan serve

dev : api-dev && app-dev

laratail:
	laratail storage/logs/laravel.log