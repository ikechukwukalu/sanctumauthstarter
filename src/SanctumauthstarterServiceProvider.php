<?php

namespace Ikechukwukalu\Sanctumauthstarter;

use Illuminate\Support\ServiceProvider;

class SanctumauthstarterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php')->prefix('api');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->loadViewsFrom(__DIR__.'/../views', 'sanctumauthstarter');
        $this->publishes([
            __DIR__.'/../views' => base_path('resources/views/ikechukwukalu/sanctumauthstarter'),
        ], 'views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\Controller::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ForgotPasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\LoginController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\LogoutController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\RegisterController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class);
        $this->app->make(\Ikechukwukalu\Sanctumauthstarter\Controllers\VerificationController::class);
    }
}
