<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests;

use Ikechukwukalu\Sanctumauthstarter\SanctumauthstarterServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Stevebauman\Location\LocationServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
      parent::setUp();
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../src/migrations');
    }

    protected function defineRoutes($router)
    {
        $router->post('api/auth/register', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\RegisterController::class,
            'register'])->name('register');

        $router->post('api/auth/login', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\LoginController::class,
            'login'])->name('login');

        $router->post('api/auth/logout', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\LogoutController::class,
            'logout'])->name('logout');

        $router->post('api/auth/logout-from-all-sessions', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\LogoutController::class,
            'logoutFromAllSessions'])->name('logoutFromAllSessions');

        $router->get('auth/verify/email/{id}', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\VerificationController::class,
            'verifyUserEmail'])->name('verification.verify');

        $router->post('api/auth/resend/verify/email', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\VerificationController::class,
            'resendUserEmailVerification'])->name('verification.resend');

        $router->post('api/auth/forgot/password', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\ForgotPasswordController::class,
            'forgotPassword'])->name('forgotPassword');

        $router->post('api/auth/reset/password', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\ResetPasswordController::class,
            'resetPassword'])->name('resetPassword');

        $router->post('api/change/password', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\ChangePasswordController::class,
            'changePassword'])->name('changePassword');

        $router->post('api/edit/profile', [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\ProfileController::class,
            'editProfile'])->name('editProfile');

        $router->post('api/create-two-factor',
            [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\TwoFactorController::class, 'createTwoFactor'])->name('createTwoFactor');

        $router->post('api/confirm-two-factor',
            [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\TwoFactorController::class, 'confirmTwoFactor'])->name('confirmTwoFactor');

        $router->post('api/disable-two-factor',
            [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\TwoFactorController::class, 'disableTwoFactor'])->name('disableTwoFactor');

        $router->post('api/current-recovery-codes',
            [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\TwoFactorController::class, 'currentRecoveryCodes'])->name('currentRecoveryCodes');

        $router->post('api/new-recovery-codes',
            [\Ikechukwukalu\Sanctumauthstarter\Tests\Controllers\TwoFactorController::class, 'newRecoveryCodes'])->name('newRecoveryCodes');
    }

    protected function getPackageProviders($app): array
    {
        return [SanctumauthstarterServiceProvider::class,
                LocationServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app) {
        $app['config']->set('auth.guards.sanctum', [
                        'driver' => 'session',
                        'provider' => 'users',
                    ]);
    }
}
