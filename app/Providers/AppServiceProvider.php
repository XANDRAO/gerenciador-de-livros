<?php

namespace App\Providers;

use App\Http\Services\GoogleBooksService;
use App\Http\Services\BrasilAPIService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BrasilAPIService::class, function ($app) {
            return new BrasilAPIService();
        });

        $this->app->singleton(GoogleBooksService::class, function ($app) {
            return new GoogleBooksService();
        });
    }

    public function boot()
    {
        //
    }
}
