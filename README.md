# SANCTUM AUTH STARTER

This is a very flexible and customisable laravel package (boilerplate) that utilises [laravel/ui](https://github.com/laravel/ui) and [laravel-sanctum](https://laravel.com/docs/9.x/sanctum) to create Basic Authetication classes for REST APIs using [Laravel](https://laravel.com/). The following functionalities are made available:

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
- Edit user profile
- Notifications
  - Welcome notification
  - Email verification
  - Login notification
  - Password change notification
  - Pin change notification
- Generate documentation
- Backup database
- Helper CI/CD files for GitHub

## REQUIREMENTS

- PHP 8+
- Laravel 9+

## STEPS TO INSTALL

- `composer require ikechukwukalu/sanctumauthstarter`
- `php artisan ui bootstrap`
- `npm install --save-dev laravel-echo@1.14.2 pusher-js@7.6.0`
- Add `pin` column to the `fillable` and `hidden` arrays within the `User` model class
- Add `'require.pin' => \Ikechukwukalu\Sanctumauthstarter\Middleware\RequirePin::class` to the `$routeMiddleware` in `kernel.php`

### PUBLISH MIGRATIONS

- `php artisan vendor:publish --tag=sas-migrations`

### PUBLISH CONFIG

- `php artisan vendor:publish --tag=sas-config`

### Social Media Login

Social media login utilizes laravel websockets to pass `access_token` to the client after authentication. First, you must setup your laravel app for [websockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction). In order to do that run the following:

- `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"`
- `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"`
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

- Add the following to your `config/services.php` file.

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_CLIENT_REDIRECT'),
],
```

- Navigate to `auth/socialite` view sample Google sign up page. Below is the script that is called within the page.

```js
window.addEventListener('DOMContentLoaded',  () => {
    const getUserUUID = () => {
        let userUUID = localStorage.getItem('user_uuid');

        if (!userUUID) {
            userUUID = crypto.randomUUID();
            localStorage.setItem('user_uuid', userUUID);
        }

        console.log('user_uuid created');
        return userUUID;
    }

    const removeUserUUID = () => {
        if (localStorage.getItem('user_uuid')) {
            localStorage.removeItem('user_uuid');
        }

        console.log('user_uuid removed');
    }

    const USER_UUID = getUserUUID();
    const TIMEOUT = parseInt("{{ $minutes }}") * 60 * 1000;

    window.Echo.channel(`access.token.${USER_UUID}`)
    .listen('.Ikechukwukalu\\Sanctumauthstarter\\Events\\SocialiteLogin', (e) => {
        console.log(`payload:`, e);
    });

    document.getElementById('googleSignUp').onclick = () => {
        window.open(
            "{{ url('set/cookie') }}/" + USER_UUID,
            '_blank'
        )
    }

    setTimeout(() => {
        removeUserUUID();
    }, TIMEOUT);
});
```

### Websockets and Queues

You will need a [queue](https://laravel.com/docs/9.x/queues#introduction) worker for the notifications and other events.

- Set `QUEUE_CONNECTION=redis` within your `.env` file.
- Uncomment `App\Providers\BroadcastServiceProvider::class` in `config\app.php`
- Your `.env` should look similar to this

```shell
PUSHER_APP_KEY=app-key
PUSHER_APP_ID=app-id
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

- Run `php artisan config:clear`, `php artisan config:cache`, `php artisan migrate`, `php artisan websockets:serve` and `php artisan queue:work --queue=high,default`
- `php artisan serve`
- `npm install && npm run dev`

## ROUTES

All routes are customisable from the config file. To override the routes copy the following routes below into your `web.php` and `api.php` respectively file.

- `web.php`

```php
Route::view(config('sanctumauthstarter.routes.web.password.reset', 'forgot/password'),
    'sanctumauthstarter::passwords.reset')->name('password.reset');
Route::post(config('sanctumauthstarter.routes.web.password.update', 'reset/password'),
    [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPasswordForm'])
    ->name('password.update');

Route::group(['middleware' => ['web']], function () {
    Route::get(config('sanctumauthstarter.routes.web.socialite.auth',
    'auth/socialite'), function() {
        return view('sanctumauthstarter::socialite.auth',
            [ 'minutes' => config('sanctumauthstarter.cookie.minutes', 5) ]);
    })->name('socialite.auth');

    Route::get(config('sanctumauthstarter.routes.web.set.cookie', 'set/cookie/{uuid}'),
        [\App\Http\Controllers\Auth\SocialiteController::class, 'setCookie'])
        ->name('set.cookie');
    Route::get(config('sanctumauthstarter.routes.web.auth.redirect', 'auth/redirect'),
        [\App\Http\Controllers\Auth\SocialiteController::class, 'authRedirect'])
        ->name('auth.redirect');
    Route::get(config('sanctumauthstarter.routes.web.auth.callback', 'auth/callback'),
        [\App\Http\Controllers\Auth\SocialiteController::class, 'authCallback'])
        ->name('auth.callback');
});
```

- `api.php`

```php
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
```

## BACKUP DATABASE

Set the following parameters in your `.env` file and run `php artisan database:backup` to backup database.

```shell
DB_BACKUP_PATH="/db/backup/${APP_NAME}"
DB_BACKUP_COMMAND="sudo mysqldump --user=${DB_USERNAME} --password=${DB_PASSWORD} --host=${DB_HOST} ${DB_DATABASE} | gzip > "
DB_BACKUP_SSH_USER=root
DB_BACKUP_SSH_HOST=127.0.0.1
DB_BACKUP_FILE="backup-${APP_NAME}-db"
DB_BACKUP_FILE_EXT=".gz"
```

## DOCUMENTATION

To generate documentation:

- `php artisan vendor:publish --tag=scribe-config`
- Change `'prefixes' => ['api/*']` to `'prefixes' => ['*']` if you want to see the docs for APIs for the  `web.php` as well.
- `php artisan scribe:generate`

Visit your newly generated docs:

- If you're using `static` type, find the `docs/index.html` file in your `public/` folder and open it in your browser.
- If you're using `laravel` type, start your app (`php artisan serve`), then visit `/docs`.

`example_languages`:
For each endpoint, an example request is shown in each of the languages specified in this array. Currently, only `bash` (curl), `javascript`(Fetch), `php` (Guzzle) and `python` (requests) are included. You can add extra languages, but you must also create the corresponding Blade view ([see Adding more example languages](https://scribe.knuckles.wtf/laravel/advanced/example-requests)).

Default: `["bash", "javascript"]`

Please visit [scribe](https://scribe.knuckles.wtf/) for more details.

## RESERVED KEYWORDS FOR PAYLOADS

- `_uuid`
- `_pin`
- `expires`
- `signature`

Some of the reserved keywords can be changed from the config file.

## TESTS

It's recommended that you run the tests before you start adding your custom models and controllers.
Make sure to keep your `database/factories/UserFactory.php` Class updated with your `users` table so that the Tests can continue to run successfully.

### Passwords

The passwords created within the `database/factories/UserFactory.php` Class must match the validation below:

``` PHP
'password' => ['required', 'string', 'max:16',
                Password::min(8)
                    ->letters()->mixedCase()
                    ->numbers()->symbols()
                    ->uncompromised(),
```

### Running Tests

- `php artisan vendor:publish --tag=sas-feature-tests`
- `php artisan serve`
- `php artisan test`

## PUBLISH VIEWS

- `php artisan vendor:publish --tag=sas-views`

## PUBLISH LANG

- `php artisan vendor:publish --tag=sas-lang`

## PUBLISH LARAVEL EMAIL NOTIFICATIONS BLADE

- `php artisan vendor:publish --tag=laravel-notifications`

## PUBLISH GITHUB WORKFLOWS

- `php artisan vendor:publish --tag=github`

## LICENSE

The Laravel package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
