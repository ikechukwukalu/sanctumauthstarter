<?php

use Illuminate\Support\Facades\Route;

/**
 * @group No Auth APIs
 *
 * APIs that do not require User authentication
 */

/**
 * @group Auth APIs
 *
 * APIs that require User authentication
 *
 * @subgroup Require Pin APIs
 */

/**
 * @group Sample APIs
 *
 * Sample APIs that require User authentication
 */

Route::prefix(config('sanctumauthstarter.routes.prefix.api.auth', 'auth'))
->group(function () {
    Route::post(config('sanctumauthstarter.routes.api.register', 'register'),
        [\App\Http\Controllers\Auth\RegisterController::class, 'register'])
        ->name('register');
    Route::post(config('sanctumauthstarter.routes.api.login', 'login'),
        [\App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->name('login');
    Route::middleware('auth:sanctum')->post(
        config('sanctumauthstarter.routes.api.logout', 'logout'),
        [\App\Http\Controllers\Auth\LogoutController::class, 'logout'])
        ->name('logout');
    Route::get(config('sanctumauthstarter.routes.api.verification.verify', 'verify/email/{id}'),
        [\App\Http\Controllers\Auth\VerificationController::class,
        'verifyUserEmail'])->name('verification.verify');
    Route::middleware('auth:sanctum')->post(
        config('sanctumauthstarter.routes.api.verification.resend', 'resend/verify/email'),
        [\App\Http\Controllers\Auth\VerificationController::class,
        'resendUserEmailVerification'])->name('verification.resend');
    Route::post(config('sanctumauthstarter.routes.api.forgotPassword', 'forgot/password'),
        [\App\Http\Controllers\Auth\ForgotPasswordController::class,
        'forgotPassword'])->name('forgotPassword');
    Route::post(config('sanctumauthstarter.routes.api.resetPassword', 'reset/password'),
    [\App\Http\Controllers\Auth\ResetPasswordController::class,
    'resetPassword'])->name('resetPassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix(config('sanctumauthstarter.routes.prefix.api.change',
    'change'))->group(function () {
        Route::post(config('sanctumauthstarter.routes.api.changePassword', 'password'),
            [\App\Http\Controllers\Auth\ChangePasswordController::class, 'changePassword'])
            ->name('changePassword');
        Route::post(config('sanctumauthstarter.routes.api.changePin', 'pin'),
            [\App\Http\Controllers\Auth\PinController::class, 'changePin'])
            ->name('changePin');
    });
    Route::post(config('sanctumauthstarter.routes.api.pinRequired', 'pin/required/{uuid}'),
        [\App\Http\Controllers\Auth\PinController::class, 'pinRequired'])
        ->name('pinRequired');
    Route::post(config('sanctumauthstarter.routes.api.editProfile', 'edit/profile'),
        [\App\Http\Controllers\Auth\ProfileController::class, 'editProfile'])
        ->name('editProfile');

    // Sample Book APIs
    Route::prefix('v1/sample/books')->group(function () {
        Route::get('{id?}', [\App\Http\Controllers\Auth\BookController::class,
        'listBooks'])->name('listBooksTest');

        // These APIs require a user's pin before requests are processed
        Route::middleware(['require.pin'])->group(function () {
            Route::post('/', [\App\Http\Controllers\Auth\BookController::class,
                'createBook'])->name('createBookTest');
            Route::patch('{id}', [\App\Http\Controllers\Auth\BookController::class,
                'updateBook'])->name('updateBookTest');
            Route::delete('{id}', [\App\Http\Controllers\Auth\BookController::class,
                'deleteBook'])->name('deleteBookTest');
        });
    });
});
