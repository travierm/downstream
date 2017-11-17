<?php

namespace App\Providers;

use Auth;
use App\MediaResolver;
use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton(MediaResolver::class, function ($app) {
           return new MediaResolver(Auth::user()->id);
       });
    }
}
