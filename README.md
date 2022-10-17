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
- `php artisan migrate`
- Add `pin` column to the `fillable` and `hidden` arrays within the `User` model class
- Add `'require.pin' => \Ikechukwukalu\Sanctumauthstarter\Middleware\RequirePin::class` to the `$routeMiddleware` in `kernel.php`

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

- `php artisan vendor:publish --tag=feature-tests`
- `php artisan serve`
- `php artisan test`

## Reserved keywords for payloads

- `_uuid`
- `_pin`
- `expires`
- `signature`

Some of the reserved keywords can be changed from the config file.

## Publish Controllers

- `php artisan vendor:publish --tag=controllers`

## Publish Models

- `php artisan vendor:publish --tag=models`

## Publish Views

- `php artisan vendor:publish --tag=views`

## Publish Routes

- `php artisan vendor:publish --tag=routes`

## Publish Lang

- `php artisan vendor:publish --tag=lang`

## Publish Config

- `php artisan vendor:publish --tag=config`

## Publish Laravel Email Notification Blade

- `php artisan vendor:publish --tag=laravel-notifications`

## License

The Laravel package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
