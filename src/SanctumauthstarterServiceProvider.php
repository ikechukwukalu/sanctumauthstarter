<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Illuminate\Support\ServiceProvider;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\ControllersCommand;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\RoutesCommand;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\SetupCommand;

class SanctumauthstarterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public const LANG = __DIR__.'/lang';
    public const DB = __DIR__.'/migrations';
    public const VIEW = __DIR__.'/views';
    public const CONFIG = __DIR__.'/config/sanctumauthstarter.php';
    public const TESTS = __DIR__.'/Tests/Feature';
    public const ACTION = __DIR__.'/.github/workflows';

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ControllersCommand::class,
                RoutesCommand::class,
                SetupCommand::class
            ]);
        }

        $this->loadMigrationsFrom(self::DB);
        $this->loadViewsFrom(self::VIEW, 'sanctumauthstarter');
        $this->loadTranslationsFrom(self::LANG, 'sanctumauthstarter');

        $this->publishes([
            self::CONFIG => config_path('sanctumauthstarter.php'),
        ], 'sas-config');
        $this->publishes([
            self::DB => database_path('migrations'),
        ], 'sas-migrations');
        $this->publishes([
            self::LANG => lang_path('vendor/sanctumauthstarter'),
        ], 'sas-lang');
        $this->publishes([
            self::VIEW => resource_path('views/vendor/sanctumauthstarter'),
        ], 'sas-views');
        $this->publishes([
            self::TESTS => base_path('tests/Feature/sanctumauthstarter'),
        ], 'sas-feature-tests');
        $this->publishes([
            self::ACTION => base_path('.github/workflows'),
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
