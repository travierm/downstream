cd app
yarn

cd ..
cd backend
composer install

cp .env.example .env
php artisan key:generate

