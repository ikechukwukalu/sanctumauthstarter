## About Sanctum Auth Starter

This is a laravel package that utilises `laravel/ui` and `laravel-sanctum` to create Basic Authetication classes for making REST APIs using [Laravel](https://laravel.com/). The following functionalities are made available:

- User registration
- User login
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

## Requirements

- PHP 8+
- Laravel 9+

## Steps To Install

- `composer require ikechukwukalu/sanctumauthstarter`
- `php artisan migrate`
- Add `pin` column to the `fillable` and `hidden` arrays within the `User` model class
- Add `'require.pin' => \Ikechukwukalu\Sanctumauthstarter\Middleware\RequirePin::class` to the `$routeMiddleware` in `kernel.php`

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

## Testing

- `php artisan vendor:publish --tag=feature-tests`
- `php artisan serve`
- `php artisan test`

## Reserved keywords for payloads

- `_uuid`
- `_pin`
- `expires`
- `signature`

Some of the reserved keywords can be changed from the config file.

## License

The Laravel package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
