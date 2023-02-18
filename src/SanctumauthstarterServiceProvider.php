<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Illuminate\Support\ServiceProvider;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\DatabaseBackUpCommand;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\ControllersCommand;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\ServiceMakeCommand;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\RoutesCommand;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\SetupCommand;

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
                DatabaseBackUpCommand::class,
                ControllersCommand::class,
                ServiceMakeCommand::class,
                RoutesCommand::class,
                SetupCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/migrations');
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
