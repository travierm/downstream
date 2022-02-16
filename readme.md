<h1>Downstream</h1>
<img src="https://travis-ci.org/travierm/downstream.svg?branch=master" />

https://downstream.us

## Development Setup

### Requirements

- PHP 7.4 or greater & Composer package manager
- Database: MySQL, Sqlite, Postgres, MariaDB (choose one)
- Latest NodeJS & Yarn
- YouTube API key for search **required**
- Spotify API key for recommendations and discovery **not required**

```php
//clone downstream repo
git clone https://github.com/Travier/downstream downstream
cd downstream

// copy new .env for laravel install
cp .env.example .env

//install PHP deps
composer install

//setup database, add your api keys
vim .env

//run migrations against database
php artisan migrate

//start php dev server
php artisan serve

// start javascipt hot reload and babel compiler
npm run hot // or 'npm run prod' to make static js,css files with cache busting
```

### Check the Wiki for fixes to common setup issues

https://github.com/travierm/downstream/wiki/Common-Setup-Issues

## References

**Media** Downstream isn't exclusively music so we refer to individual audio tracks or music video as media items. You'll see the word media a lot in the codebase it will always refer to a specific item with a media_id.

**Collecting** Users can collect any item available to Downstream. It will then be displayed in their collection page.

**Tossing** Tossing items remove them from a users collections but will keep the item for others to collect.

**Discovery** No items exist on Downstream until a user "discovers" an item through search. From there DS will recommend other items to the user using the Spotify API. These recommended items are temporary on Downstream until a user collects them and they will be processed as an official media item.

text change for CI - version 1

<h3>Powered By:</h3>
<p><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>
<a href="https://vuejs.org"><img height="90" width="90" src="https://vuejs.org/images/logo.png"></a>
