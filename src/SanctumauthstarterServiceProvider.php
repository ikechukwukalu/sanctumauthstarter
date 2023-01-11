<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Sanctumauthstarter\Console\Commands\DatabaseBackUp;

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
                DatabaseBackUp::class
            ]);
        }
        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });
        $this->loadRoutesFrom(__DIR__ . '/routes/channels.php');
        $this->loadMigrationsFrom(__DIR__. '/migrations');

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
            __DIR__.'/Controllers' => app_path('Http/Controllers/vendor/sanctumauthstarter'),
        ], 'sas-controllers');
        $this->publishes([
            __DIR__.'/Models' => app_path('Models/vendor/sanctumauthstarter'),
        ], 'sas-models');
        $this->publishes([
            __DIR__.'/Middleware' => app_path('Http/Middleware/vendor/sanctumauthstarter'),
        ], 'sas-middleware');
        $this->publishes([
            __DIR__.'/Rules' => app_path('Rules/vendor/sanctumauthstarter'),
        ], 'sas-rules');
        $this->publishes([
            __DIR__.'/routes' => base_path('routes/vendor/sanctumauthstarter'),
        ], 'sas-routes');
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

        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\Controller::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ForgotPasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\LoginController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\LogoutController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\RegisterController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\VerificationController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ChangePasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\PinController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ProfileController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\SocialiteController::class);

        // Controller for Sample Book APIs
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class);
    }
}
