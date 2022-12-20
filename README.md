# About Sanctum Auth Starter

This is a laravel package that utilises `laravel/ui` and `laravel-sanctum` to create Basic Authetication classes for REST APIs using [Laravel](https://laravel.com/). The following functionalities are made available:

- User registration
- User login
- Auto login after registration
- Login throttling
- Login 2FA (pending)
- Social media login
- Forgot password
- Email verification
- Resend email verification
- Reset password
- Change password
- Change pin
- Require pin middleware
- Require token middleware (pending)
- Change avatar (pending)
- Edit User profile
- Notifications
  - Welcome notification
  - Email verification
  - Login notification
  - Password change notification
  - Pin change notification
- Helper CI/CD files for GitHub

## Requirements

- PHP 8+
- Laravel 9+

## Steps To Install

- `composer require ikechukwukalu/sanctumauthstarter`
- Add `pin` column to the `fillable` and `hidden` arrays within the `User` model class
- Add `'require.pin' => \Ikechukwukalu\Sanctumauthstarter\Middleware\RequirePin::class` to the `$routeMiddleware` in `kernel.php`

### SOCIAL MEDIAL LOGIN

For social media login, you must setup your laravel app for websockets. In order to do that run the following:

- Run `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"`
- Run `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"`
- Set `REDIS_CLIENT=predis` and `BROADCAST_DRIVER=pusher` within your `.env` file.
- Your `laravel echo` config should look similar to this:

```js
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                .then(response => {
                    callback(false, response.data);
                })
                .catch(error => {
                    callback(true, error);
                });
            }
        };
    },
});
```

### WEBSOCKETS AND QUEUES

You will need a [queue](https://laravel.com/docs/9.x/queues#introduction) worker for the notifications and other events.

- Set `QUEUE_CONNECTION=redis` within your `.env` file.
- Run `php artisan migrate`, `php artisan websockets:serve` and `php artisan queue:work --queue=high,default`
- Run `php artisan serve`

## Routes

- `web.php`

```php
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
```

- `api.php`

```php
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
    Route::post('edit/profile', [Ikechukwukalu\Sanctumauthstarter\Controllers\ProfileController::class, 'editProfile'])->name('editProfile');


    // Sample Book APIs
    Route::prefix('v1/sample/books')->group(function () {
        Route::get('{id?}', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'listBooks'])->name('listBooksTest');

        // These APIs require a user's pin before requests are processed
        Route::middleware(['require.pin'])->group(function () {
            Route::post('/', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'createBook'])->name('createBookTest');
            Route::patch('{id}', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'updateBook'])->name('updateBookTest');
            Route::delete('{id}', [Ikechukwukalu\Sanctumauthstarter\Controllers\BookController::class, 'deleteBook'])->name('deleteBookTest');
        });
    });
});
```

## Tests

It's recommended that you run the tests before you start adding your custom models and controllers.
Make sure to keep your `database/factories/UserFactory.php` Class updated with your `users` table so that the Tests can continue to run successfully.

### NOTE

The passwords created within the `database/factories/UserFactory.php` Class must match the validation below:

``` PHP
'password' => ['required', 'string', 'max:16',
                Password::min(8)
                    ->letters()->mixedCase()
                    ->numbers()->symbols()
                    ->uncompromised(),
```

### RUNNING TESTS

- `php artisan vendor:publish --tag=sas-feature-tests`
- `php artisan serve`
- `php artisan test`

## Backup Database

Set the following parameters in your `.env` file and run `php artisan database:backup` to backup database.

``` shell
DB_BACKUP_PATH="/db/backup/${APP_NAME}"
DB_BACKUP_COMMAND="sudo mysqldump --user=${DB_USERNAME} --password=${DB_PASSWORD} --host=${DB_HOST} ${DB_DATABASE} | gzip > "
DB_BACKUP_SSH_USER=root
DB_BACKUP_SSH_HOST=127.0.0.1
DB_BACKUP_FILE="backup-${APP_NAME}-db"
DB_BACKUP_FILE_EXT=".gz"
```

## Reserved keywords for payloads

- `_uuid`
- `_pin`
- `expires`
- `signature`

Some of the reserved keywords can be changed from the config file.

## Documentation

To generate documentation:

- `php artisan vendor:publish --tag=scribe-config`
- `php artisan scribe:generate`

Visit your newly generated docs:

- If you're using `static` type, find the `docs/index.html` file in your `public/` folder and open it in your browser.
- If you're using `laravel` type, start your app (`php artisan serve`), then visit `/docs`.

`example_languages`:
For each endpoint, an example request is shown in each of the languages specified in this array. Currently, only `bash` (curl), `javascript`(Fetch), `php` (Guzzle) and `python` (requests) are included. You can add extra languages, but you must also create the corresponding Blade view ([see Adding more example languages](https://scribe.knuckles.wtf/laravel/advanced/example-requests)).

Default: `["bash", "javascript"]`

Please visit [scribe](https://scribe.knuckles.wtf/) for more details.

## Publish Controllers

- `php artisan vendor:publish --tag=sas-controllers`

## Publish Models

- `php artisan vendor:publish --tag=sas-models`

## Publish Middleware

- `php artisan vendor:publish --tag=sas-middleware`

## Publish Rules

- `php artisan vendor:publish --tag=sas-rules`

## Publish Views

- `php artisan vendor:publish --tag=sas-views`

## Publish Routes

- `php artisan vendor:publish --tag=sas-routes`

## Publish Lang

- `php artisan vendor:publish --tag=sas-lang`

## Publish Config

- `php artisan vendor:publish --tag=sas-config`

## Publish Laravel Email Notification Blade

- `php artisan vendor:publish --tag=laravel-notifications`

## Publish GitHub workflows

- `php artisan vendor:publish --tag=github`

## License

The Laravel package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
