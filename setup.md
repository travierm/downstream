## Development Setup

### Requirements

- PHP 8.1 or greater & Composer package manager
- Database: MySQL, Sqlite, Postgres, MariaDB (choose one)
- Latest NodeJS & Yarn
- YouTube API key for search **required**
- Spotify API key for recommendations and discovery **not required**

### Pre-setup requirements

#### Install PHP's Composer dependency manager

```php
wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet
```

### Setting up the application

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

// setup vue app frontend
cd vue_app/
yarn
yarn serve
```

### Check the Wiki for fixes to common setup issues

https://github.com/travierm/downstream/wiki/Common-Setup-Issues

## Dictionary

### **Media**

Downstream isn't exclusively music so we refer to video's as media items. You'll see the word media a lot in the codebase it will always refer to a specific item with a media_id.

### **Collecting**

Users can collect any item available on Downstream or through organic search. It will then be displayed on their collection page.

### **Discovery**

No item exists on Downstream until a user discovers an item through search. That item is then used as a seed for our discovery service to find additional items related to what you collected.

These discovered items are temporary on Downstream until a user collects them and then they become a discovered media item on the site.
