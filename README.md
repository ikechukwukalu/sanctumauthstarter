# SANCTUM AUTH STARTER

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ikechukwukalu/sanctumauthstarter?style=flat-square)](https://packagist.org/packages/ikechukwukalu/sanctumauthstarter)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/ikechukwukalu/sanctumauthstarter/main?style=flat-square)](https://scrutinizer-ci.com/g/ikechukwukalu/sanctumauthstarter/)
[![Code Quality](https://img.shields.io/codefactor/grade/github/ikechukwukalu/sanctumauthstarter?style=flat-square)](https://www.codefactor.io/repository/github/ikechukwukalu/sanctumauthstarter)
[![Vulnerability](https://img.shields.io/snyk/vulnerabilities/github/ikechukwukalu/sanctumauthstarter?style=flat-square)](https://security.snyk.io/package/composer/ikechukwukalu%2Fsanctumauthstarter)
[![Github Workflow Status](https://img.shields.io/github/actions/workflow/status/ikechukwukalu/sanctumauthstarter/sanctumauthstarter.yml?branch=main&style=flat-square)](https://github.com/ikechukwukalu/sanctumauthstarter/actions/workflows/sanctumauthstarter.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ikechukwukalu/sanctumauthstarter?style=flat-square)](https://packagist.org/packages/ikechukwukalu/sanctumauthstarter)
[![Licence](https://img.shields.io/packagist/l/ikechukwukalu/sanctumauthstarter?style=flat-square)](https://packagist.org/packages/ikechukwukalu/sanctumauthstarter)

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
- Edit user profile
- Notifications
  - Welcome notification
  - Email verification
  - Login notification
  - Password change notification
- Generate documentation
- Helper CI/CD files for GitHub

## REQUIREMENTS

- PHP 8+
- Laravel 9+

## STEPS TO INSTALL

``` shell
composer require ikechukwukalu/sanctumauthstarter
```

- `php artisan ui bootstrap`
- `npm install --save-dev laravel-echo pusher-js`
- Uncomment `use Illuminate\Contracts\Auth\MustVerifyEmail;` in `User` model class
- Add `two_factor` columns to the `fillable` and `hidden` arrays within the `User` model class. At the end the `User` should look similar to this:

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
        'two_factor',
        'socialite_signup',
        'form_signup'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
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

## GENERATE AUTH CONTROLLERS, REQUESTS, SERVICES AND ROUTES

You can run `php artisan sas:setup` to generate them at once. You can also call generate them separately:

- `php artisan sas:controllers`
- `php artisan sas:routes`
- `php artisan sas:tests`

## PUBLISH MIGRATIONS AND CONFIG

- `php artisan vendor:publish --tag=sas-migrations`
- `php artisan vendor:publish --tag=sas-config`

## WEBSOCKETS AND QUEUES

This package utilizes laravel [beyondcode/laravel-websockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction) to pass `access_token` to the client after authentication. First, you must setup your laravel app for broadcasts. In order to do that run the following:

- `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"`
- `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"`
- Set `REDIS_CLIENT=predis` and `BROADCAST_DRIVER=pusher` within your `.env` file.
- Your `laravel-echo` config should look similar to this:

``` js
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    cluster:import.meta.env.VITE_PUSHER_APP_CLUSTER,
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

You will need a [queue](https://laravel.com/docs/9.x/queues#introduction) worker for the notifications and other events.

- Set `QUEUE_CONNECTION=redis` within your `.env` file.
- Uncomment `App\Providers\BroadcastServiceProvider::class` in `config\app.php`
- Your `.env` should look similar to this

``` shell
PUSHER_APP_KEY=app-key
PUSHER_APP_ID=app-id
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

- For SSL using apache. The snippet below should be placed within the `virtualhost` SSL config.

``` shell
    ProxyPass "/app/" "ws://127.0.0.1:6001/app/"
    ProxyPass "/app/" "http://127.0.0.1:6001/app/"
```

- Run `php artisan config:clear`, `php artisan migrate`, `php artisan websockets:serve` and `php artisan queue:work`
- `php artisan serve`
- `npm install && npm run dev`

## WEBVIEW LOGINS

- Social media login
- Two-factor login

### Social Media Login

Add the following to your `config/services.php` file.

``` php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_CLIENT_REDIRECT'),
],
```

- Navigate to `auth/socialite` to view a sample Google sign-up/sign-in page to view the generated `access_token` after sign up. Uncomment the route within the `web.php`. Below is the script that is called within the view `resources/views/vendor/sanctumauthstarter/socialite/auth.blade.php`.

``` js
window.addEventListener('DOMContentLoaded',  () => {
    const getUserUUID = () => {
        let userUUID = localStorage.getItem('user_uuid');

        if (!userUUID) {
            userUUID = crypto.randomUUID();
            localStorage.setItem('user_uuid', userUUID);
        }

        console.log('user_uuid created', userUUID);
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

    window.Echo.channel(`access.token.socialite.${USER_UUID}`)
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

- After a successful authentication, this view is displayed `resources/views/vendor/sanctumauthstarter/socialite/callback.blade.php` and it contains the following script:

``` js
window.addEventListener('DOMContentLoaded',  () => {
    if (localStorage.getItem('user_uuid')) {
        localStorage.removeItem('user_uuid');
    }
});
```

### Two-factor Login

This package utilizes [Laragear/TwoFactor](https://github.com/Laragear/TwoFactor) to power 2fa login and [beyondcode/laravel-websockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction) to pass `access_token` to the client after authentication.

2fa authentication has been implemented for both password login and social media login.

- `php artisan vendor:publish --provider="Laragear\TwoFactor\TwoFactorServiceProvider"`
- `php artisan migrate`
- Replace the form in `resources/views/vendor/two-factor/login.blade.php` with the code below:

``` php
<form method="get">
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

- Call `api/create-two-factor` to create 2fa.

``` json
{
    "status": "success",
    "status_code": 200,
    "data": {
        "qr_code": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<svg...",
        "uri": "otpauth://totp/%3Ajohndoe@xyz.com?label=johndoe%40xyz.com&secret=EQGRSNGAE3TREOT7XOLB5VVRS42LLXYS&algorithm=SHA1&digits=6",
        "string": "EQGRSNGAE3TREOT7XOLB5VVRS42LLXYS"
    }
}
```

- Call `api/confirm-two-factor` to confirm 2fa and get recovery codes.

``` json
{
    "status": "success",
    "status_code": 200,
    "data": {
        "message": "Download your recover codes and keep them safe!",
        "codes": [
            {
                "code": "SV6OH71D",
                "used_at": null
            },
            {
                "code": "62KEBSNE",
                "used_at": null
            },
            {
                "code": "YSVHOG9X",
                "used_at": null
            },
            ...
        ]
    }
}
```

- Call `api/disable-two-factor` to disable 2fa, `api/current-recovery-codes` to retrieve current recovery codes and `api/new-recovery-codes` to generate new recovery codes which replaces the previous batch.

### Two factor enabled for password login

When 2fa has been enabled and a user attempts to login, a payload would be returned that contains a `user_uuid` and a `twofactor_url`.

``` json
{
    "status": "success",
    "status_code": 200,
    "data": {
        "twofactor_url": "http://127.0.0.1:8000/twofactor/required/johndoe@xyz.com/0220dbe7-08dc-470e-b1e2-4411ba155bc1",
        "user_uuid": "0220dbe7-08dc-470e-b1e2-4411ba155bc1",
        "access_token": null,
        "message": "Two-Factor Authentication is required!"
    }
}
```

The `user_uuid` is used to create a `laravel-echo` channel that would listen to a laravel broadcast. Navigate to `auth/twofactor/{email}/{uuid}` to view the generated `access_token` after uncommenting the route within the `web.php`. This view `resources/views/vendor/sanctumauthstarter/twofactor/auth.blade.php` contains a sample `javascript` that works it out.

``` js
window.addEventListener('DOMContentLoaded',  () => {
    const USER_UUID = "{{ Route::input('uuid') }}";

    console.log(USER_UUID);

    window.Echo.channel(`access.token.twofactor.${USER_UUID}`)
    .listen('.Ikechukwukalu\\Sanctumauthstarter\\Events\\TwoFactorLogin', (e) => {
        console.log(`payload:`, e);
    });
});
```

### Two factor enabled for social media login

When 2fa has been enabled a 2fa page will pop up over your browser.

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

- `php artisan test`

## RECOMMENED PACKAGES

- [ikechukwukalu/makeservice](https://github.com/ikechukwukalu/makeservice)

``` shell
composer require ikechukwukalu/makeservice
```

- [ikechukwukalu/databasebackup](https://github.com/ikechukwukalu/databasebackup)

``` shell
composer require ikechukwukalu/databasebackup
```

- [ikechukwukalu/requirepin](https://github.com/ikechukwukalu/requirepin)

``` shell
composer require ikechukwukalu/requirepin
```

- [ikechukwukalu/clamavfileupload](https://github.com/ikechukwukalu/clamavfileupload)

``` shell
composer require ikechukwukalu/clamavfileupload
```

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
