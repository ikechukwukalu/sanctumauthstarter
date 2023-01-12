<?php

use Illuminate\Support\Facades\Route;

/**
 * @group Web URLs
 *
 * APIs that do not require User autherntication and is performed over a web browser
 */

Route::view('forgot/password', 'sanctumauthstarter::passwords.reset')->name('password.reset');
Route::post('reset/password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPasswordForm'])->name('password.update');

Route::group(['middleware' => ['web']], function () {
    /**
     * User Socialite Auth.
     *
     * This API opens a view that sets a <b>UUID</b> and stores it as a localStorage.
     *
     * Using the stored <b>UUID</b>, it subscribes the user to a unique public websocket channel
     * using laravel <b>Echo</b>, which will receive the <b>access_token</b> and <b>user_id</b>
     * as return payloads when the Oauth login is completed.
     *
     * It also provides a link to a <small class="badge badge-blue">set/cookie/{uuid}</small> using the stored <b>UUID</b>
     * that starts the Oauth authentication process.
     *
     * @header Accept text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*\/*;q=0.8,application/signed-exchange;v=b3;q=0.9
     * @header Content-Type text/html; charset=UTF-8
     *
     * @response 200 return view('sanctumauthstarter::socialite.auth')
     *
     * @group Web URLs
     */
    Route::get('auth/socialite', function() {
        return view('sanctumauthstarter::socialite.auth',
            [ 'minutes' => config('sanctumauthstarter.cookie.minutes', 5) ]);
    })->name('socialite.auth');

    Route::get('set/cookie/{uuid}', [\App\Http\Controllers\Auth\SocialiteController::class, 'setCookie'])->name('set.cookie');
    Route::get('auth/redirect', [\App\Http\Controllers\Auth\SocialiteController::class, 'authRedirect'])->name('auth.redirect');
    Route::get('auth/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'authCallback'])->name('auth.callback');
});
