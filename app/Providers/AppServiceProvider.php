<?php

namespace App\Providers;

use App\Models\Cat;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (scheme() == 'https') {
            URL::forceScheme('https');
        }

        Http::macro('feed', function () {
            $d = config('feed.api_url');
            return Http::baseUrl(config('feed.api_url'))
                ->retry(3)
                ->timeout(30);
        });

        if (!app()->runningInConsole()) {
            View::share('menu', Cat::where('p_id', -1)->limit(5)->get());
        }
    }
}
