<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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

Route::prefix('auth')->group(function () {
   Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
    Route::middleware('auth:sanctum')->post('logout', [\App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
    Route::get('verify/email/{id}', [\App\Http\Controllers\Auth\VerificationController::class, 'verifyUserEmail'])->name('verification.verify');
    Route::middleware('auth:sanctum')->post('resend/verify/email', [\App\Http\Controllers\Auth\VerificationController::class, 'resendUserEmailVerification'])->name('verification.resend');
    Route::post('forgot/password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('reset/password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword'])->name('resetPassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('change')->group(function () {
        Route::post('password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'changePassword'])->name('changePassword');
        Route::post('pin', [\App\Http\Controllers\Auth\PinController::class, 'changePin'])->name('changePin');
    });
    Route::post('pin/required/{uuid}', [\App\Http\Controllers\Auth\PinController::class, 'pinRequired'])->name(config('sanctumauthstarter.pin.route', 'require_pin'));
    Route::post('edit/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'editProfile'])->name('editProfile');


    // Sample Book APIs
    Route::prefix('v1/sample/books')->group(function () {
        Route::get('{id?}', [\App\Http\Controllers\Auth\BookController::class, 'listBooks'])->name('listBooksTest');

        // These APIs require a user's pin before requests are processed
        Route::middleware(['require.pin'])->group(function () {
            Route::post('/', [\App\Http\Controllers\Auth\BookController::class, 'createBook'])->name('createBookTest');
            Route::patch('{id}', [\App\Http\Controllers\Auth\BookController::class, 'updateBook'])->name('updateBookTest');
            Route::delete('{id}', [\App\Http\Controllers\Auth\BookController::class, 'deleteBook'])->name('deleteBookTest');
        });
    });
});
