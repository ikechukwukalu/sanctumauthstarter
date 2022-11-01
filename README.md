# About Sanctum Auth Starter

This is a laravel package that utilises `laravel/ui` and `laravel-sanctum` to create Basic Authetication classes for making REST APIs using [Laravel](https://laravel.com/). The following functionalities are made available:

- User registration
- User login
- Auto login after registration
- Login throttling
- Login 2FA (pending)
- Social media login (pending)
- Forgot password
- Email verification
- Resend email verification
- Reset password
- Change password
- Change pin
- Require pin middleware
- Change avatar (pending)
- Notifications
  - Welcome notification
  - Email verification
  - Login notification
  - Password change notification
  - Pin change notification

## Requirements

- PHP 8+
- Laravel 9+

## Steps To Install

- `composer require ikechukwukalu/sanctumauthstarter`
- You will need a [queue](https://laravel.com/docs/9.x/queues#introduction) worker for the notifications. For a quick start set `QUEUE_CONNECTION=database` within your `.env` file.
- Run `php artisan queue:table`, `php artisan migrate` and `php artisan queue:work --queue=high,default`
- Add `pin` column to the `fillable` and `hidden` arrays within the `User` model class
- Add `'require.pin' => \Ikechukwukalu\Sanctumauthstarter\Middleware\RequirePin::class` to the `$routeMiddleware` in `kernel.php`
- Run `php artisan serve`

## Tests

It's recommended that you run the tests before you start adding your models and controllers.
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

## License

The Laravel package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
