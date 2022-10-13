<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [Ikechukwukalu\Sanctumauthstarter\Controllers\RegisterController::class, 'register'])->name('register');
    Route::post('login', [Ikechukwukalu\Sanctumauthstarter\Controllers\LoginController::class, 'login'])->name('login');
    Route::middleware('auth:sanctum')->post('logout', [Ikechukwukalu\Sanctumauthstarter\Controllers\LogoutController::class, 'logout'])->name('logout');
    Route::get('verify/email/{id}', [Ikechukwukalu\Sanctumauthstarter\Controllers\VerificationController::class, 'verifyUserEmail'])->name('verification.verify');
    Route::middleware('auth:sanctum')->post('resend/verify/email', [Ikechukwukalu\Sanctumauthstarter\Controllers\VerificationController::class, 'resendUserEmailVerification'])->name('verification.resend');
    Route::post('forgot/password', [Ikechukwukalu\Sanctumauthstarter\Controllers\ForgotPasswordController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('reset/password', [Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class, 'resetPassword'])->name('resetPassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('change')->group(function () {
        Route::post('password', [Ikechukwukalu\Sanctumauthstarter\Controllers\ChangePasswordController::class, 'changePassword'])->name('changePassword');
        Route::post('pin', [Ikechukwukalu\Sanctumauthstarter\Controllers\PinController::class, 'changePin'])->name('changePin');
    });
    Route::post('pin/required/{uuid}', [Ikechukwukalu\Sanctumauthstarter\Controllers\PinController::class, 'pinRequired'])->name(config('sanctumauthstarter.pin.route', 'require_pin'));

    Route::get('external-books', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'externalBooks'])->name('externalBooks');

    Route::middleware(['require.pin'])->group(function () {
        Route::prefix('v1/books')->group(function () {
            Route::post('/', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'createBook'])->name('createBook');
            Route::get('{id?}', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'listBooks'])->name('listBooks');
            Route::patch('{id}', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'updateBook'])->name('updateBook');
            Route::delete('{id}', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'deleteBook'])->name('deleteBook');
        });
    });
});
