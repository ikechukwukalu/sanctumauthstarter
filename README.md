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
- Change pin (pending)
- Require pin middleware (pending)
- Change avatar (pending)

## Requirements

- PHP 8+
- Laravel 9+

## Steps To Install

- `composer require ikechukwukalu/sanctumauthstarter`
- `php artisan vendor:publish --tag=sas-views`
- `php artisan migrate`

## Publish Controllers

- `php artisan vendor:publish --tag=sas-controllers`

## Publish Routes

- `php artisan vendor:publish --tag=sas-routes`

## Publish Lang

- `php artisan vendor:publish --tag=sas-lang`

## Publish Laravel Email Notification Blade

- `php artisan vendor:publish --tag=laravel-notifications`

## Testing

- `php artisan vendor:publish --tag=sas-feature-tests`
- `php artisan serve`
- `php artisan test`

## Reserved keywords for payloads

- `_uuid`
- `_pin`
- `expires`
- `signature`

## License

The Laravel package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
