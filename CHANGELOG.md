# v2.0.2

- Updated json response to reflect error status

## v2.0.1

- Updated `composer.json`

## v2.0.0

- Refactored code
- Removed Static methods

## v1.3.2

- Removed queue name from UserEventListener class
- Fixed Password reset bug

## v1.3.1

- Improved sample auth blades for 2FA and socialite packages
- Fixed wrong route for email notification

## v1.3.0

Separated the following functionalities into standalone packages:

- Require pin middleware
- Database back up
- Service scaffolding

Additional Changes:

- Added tests for package
- Removed migrations and classes related to separated functionalities
- Added Laravel 10 support
- Added Test scaffolding
