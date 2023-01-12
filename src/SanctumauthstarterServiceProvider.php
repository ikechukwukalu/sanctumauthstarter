<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\DatabaseBackUp;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\ControllersCommand;

class SanctumauthstarterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DatabaseBackUp::class,
                ControllersCommand::class,
            ]);
        }

        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });

        $this->loadRoutesFrom(__DIR__ . '/routes/channels.php');

        $this->loadViewsFrom(__DIR__.'/views', 'sanctumauthstarter');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'sanctumauthstarter');

        $this->publishes([
            __DIR__.'/config/sanctumauthstarter.php' => config_path('sanctumauthstarter.php'),
        ], 'sas-config');
        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ], 'sas-migrations');
        $this->publishes([
            __DIR__.'/lang' => lang_path('vendor/sanctumauthstarter'),
        ], 'sas-lang');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/sanctumauthstarter'),
        ], 'sas-views');
        $this->publishes([
            __DIR__.'/Tests/Unit' => base_path('tests/Unit/sanctumauthstarter'),
        ], 'sas-unit-tests');
        $this->publishes([
            __DIR__.'/Tests/Feature' => base_path('tests/Feature/sanctumauthstarter'),
        ], 'sas-feature-tests');
        $this->publishes([
            __DIR__.'/.github/workflows' => base_path('.github/workflows'),
        ], 'github');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/sanctumauthstarter.php', 'sanctumauthstarter'
        );

        $this->app->register(EventServiceProvider::class);
    }
}
