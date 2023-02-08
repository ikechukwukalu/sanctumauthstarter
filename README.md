# SANCTUM AUTH STARTER

This is a very flexible and customisable laravel package (boilerplate) that utilises [laravel/ui](https://github.com/laravel/ui) and [laravel-sanctum](https://laravel.com/docs/9.x/sanctum) to create Basic Authetication classes and other helpful functionalities to give you a quick start when building REST APIs using [Laravel](https://laravel.com/). The following functionalities are made available:

- User registration
- User login
- Auto login after registration
- Login throttling
- Login 2FA
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

- `composer require ikechukwukalu/sanctumauthstarter -W`
- `php artisan ui bootstrap`
- `npm install --save-dev laravel-echo@1.14.2 pusher-js@7.6.0`
- Add `pin` and `two_factor` columns to the `fillable` and `hidden` arrays within the `User` model class. At the end the `User` should look similar to this:

``` php
use Laravel\Sanctum\HasApiTokens;
use Laragear\TwoFactor\TwoFactorAuthentication;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;

class User extends Authenticatable implements TwoFactorAuthenticatable, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthentication;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pin',
        'two_factor'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'pin',
        'remember_token',
        'two_factor',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```

- Add `'require.pin' => \Ikechukwukalu\Sanctumauthstarter\Middleware\RequirePin::class` to the `$routeMiddleware` in `kernel.php`

### GENERATE AUTH CONTROLLERS, REQUESTS, SERVICES AND ROUTES

You can run `php artisan sas:setup` to generate them at once. You can also call generate them separately:

- `php artisan sas:controllers`
- `php artisan sas:routes`

This package comes with an example `BookController` and it's respective api routes for the provided `require.pin` middleware. To generate them alongside the controllers and routes, run the following command `php artisan sas:setup --sample`. You can also call generate them separately:

- `php artisan sas:controllers --sample`
- `php artisan sas:routes --sample`

#### Note

All routes are customisable from the config file.

To generate a new service class.

- `php artisan make:service SampleService`
- `php artisan make:service SampleService --force`. This will overwrite and existing service class.

To generate a new service class for a particular request class.

- `php artisan make:request SampleRequest`
- `php artisan make:service SampleService --request=SampleRequest`

### PUBLISH MIGRATIONS

- `php artisan vendor:publish --tag=sas-migrations`

### PUBLISH CONFIG

- `php artisan vendor:publish --tag=sas-config`

### Two Factor Login

Two factor login utilizes laravel websockets to pass `access_token` to the client after authentication. See [Laragear/TwoFactor](https://github.com/Laragear/TwoFactor) for more information. 2fa authentication has been implemented for both password login and social media login.

- php artisan vendor:publish --provider="Laragear\TwoFactor\TwoFactorServiceProvider"
- Replace the form in `resources/views/vendor/two-factor/login.blade.php` with the code below:

``` php
<form id="twofactor" method="get">
    @php
        foreach ($_GET as $key => $value) {
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);
            echo "<input type='hidden' name='$key' value='$value'/>";
        }
    @endphp
    @csrf
    <p class="text-center">
        {{ trans('two-factor::messages.continue') }}
    </p>
    <div class="form-row justify-content-center py-3">
        @if($errors->isNotEmpty() || isset($message))
            <div class="col-12 alert alert-danger pb-0">
                <ul>
                    @if (isset($message))
                        <li>{{ $message }}</li>
                    @endif
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-sm-8 col-8 mb-3">
            <input type="text" name="{{ $input }}" id="{{ $input }}"
                    class="@error($input) is-invalid @enderror form-control form-control-lg"
                    minlength="6" placeholder="123456" required>
        </div>
        <div class="w-100"></div>
        <div class="col-auto mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                {{ trans('two-factor::messages.confirm') }}
            </button>
        </div>
    </div>
</form>
```

- `api/create-two-factor` to create 2fa, `api/confirm-two-factor` to confirm 2fa and activate recovery codes, `api/disable-two-factor` to disable 2fa and `api/new-recovery-codes` to generate new recovery codes which replaces the previous batch.

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

- Navigate to `auth/socialite` to view sample Google sign up page. Below is the script that is called within that page.

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
            "{{ url('auth/redirect') }}/" + USER_UUID,
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

- For SSL using apache. The snippet below should be placed within the `virtualhost` SSL config.

```apache
    ProxyPass "/app/" "ws://127.0.0.1:6001/app/"
    ProxyPass "/app/" "http://127.0.0.1:6001/app/"
```

- Run `php artisan config:clear`, `php artisan migrate`, `php artisan websockets:serve` and `php artisan queue:work --queue=high,default`
- `php artisan serve`
- `npm install && npm run dev`

## BACKUP DATABASE

Set the following parameters in your `.env` file and run `php artisan database:backup` to backup database.

```shell
DB_BACKUP_PATH="/db/backup/${APP_NAME}"
DB_BACKUP_COMMAND="sudo mysqldump --user=${DB_USERNAME} --password=${DB_PASSWORD} --host=${DB_HOST} ${DB_DATABASE} | gzip > "
DB_BACKUP_SSH_USER=root
DB_BACKUP_SSH_HOST=127.0.0.1
DB_BACKUP_FILE="backup-${APP_NAME}-db"
DB_BACKUP_FILE_EXT=".gz"
DB_REMOTE_ACCESS=false
```

## DOCUMENTATION

To generate documentation:

- `composer require --dev knuckleswtf/scribe`
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

The SAS package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
