<?php

namespace App\Providers;

use App\Services\OtakudesuApiService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('RENDER')) {
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        View::composer('layouts.*', function ($view) {
            try {
                $genres = request()->routeIs('admin.*', 'login', 'register')
                    ? collect()
                    : app(OtakudesuApiService::class)->getGenres();
            } catch (Throwable) {
                $genres = collect();
            }

            $view->with('navGenres', $genres);
        });
    }
}
