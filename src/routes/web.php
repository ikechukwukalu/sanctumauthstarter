<?php

use Illuminate\Support\Facades\Route;

/**
 * @group Web APIs
 *
 * APIs that do not require User autherntication and is performed over a web browser
 */

Route::view('forgot/password', 'sanctumauthstarter::passwords.reset')->name('password.reset');
Route::post('reset/password', [Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class, 'resetPasswordForm'])->name('password.update');

Route::group(['middleware' => ['web']], function () {
    Route::get('auth/socialite', function() {
        return view('sanctumauthstarter::socialite.auth');
    })->name('socialite.auth');

    Route::get('set/cookie/{uuid}', [Ikechukwukalu\Sanctumauthstarter\Controllers\SocialiteRegisterController::class, 'setCookie'])->name('set.cookie');
    Route::get('auth/redirect', [Ikechukwukalu\Sanctumauthstarter\Controllers\SocialiteRegisterController::class, 'authRedirect'])->name('auth.redirect');
    Route::get('auth/callback', [Ikechukwukalu\Sanctumauthstarter\Controllers\SocialiteRegisterController::class, 'authCallback'])->name('auth.callback');
});
