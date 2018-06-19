<h1>Downstream</h1>
<img src="https://travis-ci.com/Travier/downstream.svg?token=WQrNcAcxWXTGaqEEdVh4&branch=master" />

#### A music discovery and collection service built ontop of the worlds largest media services (YouTube, Spotify)

<h3>Powered By:</h3>
<p><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>
<a href="https://vuejs.org"><img height="90" width="90" src="https://vuejs.org/images/logo.png"></a>

## Setup for Development
- Have PHP 7.0 >, composer and a recent NodeJS version installed
```bash
git clone https://github.com/Travier/downstream downstream
cd downstream
// copy new .env for laravel install
cp .env.example .env
composer install
//setup .env with mysql connection then run migrate to install tables
php artisn migrate
//start php dev server or use (apache, nginx) to run laravel folder
php artisan serve
// start javascipt hot reload and babel compiler
npm run hot // or 'npm run prod' to make static js,css files with cache busting
// you're good to go!
```


## Screenshots
<img src="https://s8.postimg.cc/dr0k6lw2t/image.png"/>
<img src="https://s8.postimg.cc/f7c2odgvp/image.png"/>
<img src=|"https://s8.postimg.cc/f7c2ogwc5/image.png"/>
<img src="https://s8.postimg.cc/l89rllo45/image.png"/>
<img src="https://s8.postimg.cc/bnq4ytto5/image.png"/>

## Concepts
DS => Downstream

**Media** DS isn't exclusively music so we refer to individual audios tracks or music video as media items. You'll see the word media a lot in the codebase it will always refer to a specific item with a media_id.

**Discovery** No items exist on DS until a user "discovers" an item through search. From there DS will recommend other items to the user using the Spotify API. These recommended items are temporary on DS until a user collects them and they will be processed as an official media item.

**Collecting** Users can collect any item available to DS. It will then be displayed in their collection page.

**Tossing** Tossing items remove them from a users collections but will keep the item for others to collect
