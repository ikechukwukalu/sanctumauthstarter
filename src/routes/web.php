<?php

use Illuminate\Support\Facades\Route;

Route::view('forgot/password', 'sanctumauthstarter::passwords.reset')->name('password.reset');
Route::post('reset/password', [Ikechukwukalu\Sanctumauthstarter\Controllers\ResetPasswordController::class, 'resetPasswordForm'])->name('password.update');
