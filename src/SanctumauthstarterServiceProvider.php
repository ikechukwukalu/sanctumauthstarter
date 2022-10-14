<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SanctumauthstarterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });

        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'sanctumauthstarter');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'sanctumauthstarter');

        $this->publishes([
            __DIR__.'/config' => base_path('resources/lang/ikechukwukalu/sanctumauthstarter'),
        ], 'config');
        $this->publishes([
            __DIR__.'/lang' => base_path('resources/lang/ikechukwukalu/sanctumauthstarter'),
        ], 'lang');
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/ikechukwukalu/sanctumauthstarter'),
        ], 'views');
        $this->publishes([
            __DIR__.'/Controllers' => base_path('app/Http/Controllers/ikechukwukalu/sanctumauthstarter'),
        ], 'controllers');
        $this->publishes([
            __DIR__.'/Models' => base_path('app/Models/ikechukwukalu/sanctumauthstarter'),
        ], 'models');
        $this->publishes([
            __DIR__.'/routes' => base_path('routes/ikechukwukalu/sanctumauthstarter'),
        ], 'routes');
        $this->publishes([
            __DIR__.'/Tests/Unit' => base_path('tests/Unit/ikechukwukalu/sanctumauthstarter'),
        ], 'unit-tests');
        $this->publishes([
            __DIR__.'/Tests/Feature' => base_path('tests/Feature/ikechukwukalu/sanctumauthstarter'),
        ], 'feature-tests');
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

        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\Controller::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ForgotPasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\LoginController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\LogoutController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\RegisterController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\VerificationController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ChangePasswordController::class);
    }
}
