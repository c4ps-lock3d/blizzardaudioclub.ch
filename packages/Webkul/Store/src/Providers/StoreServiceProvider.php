<?php

namespace Webkul\Store\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Store\Http\Middleware\AuthenticateCustomer;
use Webkul\Store\Http\Middleware\Currency;
use Webkul\Store\Http\Middleware\Locale;
use Webkul\Store\Http\Middleware\Theme;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        /* loaders */
        Route::middleware('web')->group(__DIR__.'/../Routes/web.php');
        Route::middleware('web')->group(__DIR__.'/../Routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'store');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'store');

        /* aliases */
        $router->aliasMiddleware('currency', Currency::class);
        $router->aliasMiddleware('locale', Locale::class);
        $router->aliasMiddleware('customer', AuthenticateCustomer::class);
        $router->aliasMiddleware('theme', Theme::class);

        $this->publishes([
            dirname(__DIR__).'/Config/imagecache.php' => config_path('imagecache.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('themes/MyTheme/views'),
    
        ]);

        /* Paginator */
        Paginator::defaultView('shop::partials.pagination');
        Paginator::defaultSimpleView('shop::partials.pagination');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'shop');

        /* Breadcrumbs */
        require __DIR__.'/../Routes/breadcrumbs.php';

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/menu.php',
            'menu.customer'
        );
    }
}
