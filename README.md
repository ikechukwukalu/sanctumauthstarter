## About Sanctum Auth Starter

This is a laravel package that utilises `laravel/ui` and `laravel-sanctum` to create Basic Authetication classes for making REST APIs using Laravel. The following functionalities are made available:

- User registration
- User login
- Login throttling
- Forgot password
- Email verification
- Resend email verification
- Reset password

## Requirements

- PHP 8+
- Laravel 9+

## Steps To Install

- `composer require ikechukwukalu/sanctumauthstarter`
- `php artisan vendor:publish --tag=views`
- `php artisan migrate`

## Publish Controllers

- `php artisan vendor:publish --tag=controllers`

## Publish Routes

- `php artisan vendor:publish --tag=routes`

## Testing

- `php artisan serve`
- `php artisan vendor:publish --tag=feature-tests`
- `php artisan test`


