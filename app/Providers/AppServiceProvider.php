<?php

namespace App\Providers;

use App\Services\GoogleBooksService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GoogleBooksService::class, function ($app) {
            return new GoogleBooksService();
        });
    }

    public function boot()
    {
        //
    }
}
