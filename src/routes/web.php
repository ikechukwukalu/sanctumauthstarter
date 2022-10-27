<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * @group No Auth APIs
 *
 * APIs that do not require User autherntication
 */
Route::view('forgot/password', 'sanctumauthstarter::passwords.reset')->name('password.reset');
Route::post('reset/password', [Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class, 'resetPasswordForm'])->name('password.update');
