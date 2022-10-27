# Changelog

All notable changes to `data-transfer-object` will be documented in this file

## 3.9.0 - 2022-09-14

### What's Changed

- allow enum casting not only enum value casting by @regnerisch in https://github.com/spatie/data-transfer-object/pull/301

### New Contributors

- @regnerisch made their first contribution in https://github.com/spatie/data-transfer-object/pull/301

**Full Changelog**: https://github.com/spatie/data-transfer-object/compare/3.8.1...3.9.0

## 3.8.1 - 2022-06-02

### What's Changed

- Fixed bug #249 by @gerpo in https://github.com/spatie/data-transfer-object/pull/251

**Full Changelog**: https://github.com/spatie/data-transfer-object/compare/3.8.0...3.8.1

## 3.8.0 - 2022-06-02

### What's Changed

- Added enum caster by @Elnadrion in https://github.com/spatie/data-transfer-object/pull/277

### New Contributors

- @Raja-Omer-Mustafa made their first contribution in https://github.com/spatie/data-transfer-object/pull/271
- @Elnadrion made their first contribution in https://github.com/spatie/data-transfer-object/pull/277

**Full Changelog**: https://github.com/spatie/data-transfer-object/compare/3.7.3...3.8.0

## 3.7.3 - 2022-01-10

## What's Changed

- Stop suggesting phpstan/phpstan by @ymilin in https://github.com/spatie/data-transfer-object/pull/264

## New Contributors

- @sergiy-petrov made their first contribution in https://github.com/spatie/data-transfer-object/pull/246
- @damms005 made their first contribution in https://github.com/spatie/data-transfer-object/pull/255
- @ymilin made their first contribution in https://github.com/spatie/data-transfer-object/pull/264

**Full Changelog**: https://github.com/spatie/data-transfer-object/compare/3.7.2...3.7.3

## 3.7.2 - 2021-09-17

- `#[Strict]` is passed down the inheritance chain so children are strict when parent is strict (#239)

## 3.7.1 - 2021-09-09

- Cast properties with self or parent type (#236)

## 3.7.0 - 2021-08-26

- Add `#[MapTo]` support (#233)

## 3.6.2 - 2021-08-25

- Correct behavior of Arr::forget with dot keys (#231)

## 3.6.1 - 2021-08-17

- Fix array assignment bug with strict dto's (#225)

## 3.6.0 - 2021-08-12

- Support mapped properties (#224)

## 3.5.0 - 2021-08-11

- Support union types in casters (#210)

## 3.4.0 - 2021-08-10

- Fix for an empty value being created when casting `ArrayAccess` objects (#216)
- Add logic exception when attempting to cast `ArrayAccess` objects that are not traversable (#216)
- Allow the `ArrayCaster` to retain values that are already instances of the `itemType` (#217)

## 3.3.0 - 2021-06-01

- Expose DTO and validation error array in ValidationException (#213)

## 3.2.0 - 2021-05-31

- Support generic casters (#199)
- Add `ArrayCaster`
- Add casting of objects that implement `ArrayAccess` to the `ArrayCaster` (#206)
- Fix for caster subclass check (#204)

## 3.1.1 - 2021-04-26

- Make `DefaultCast` repeatable (#202)

## 3.1.0 - 2021-04-21

- Add `DataTransferObject::clone(...$args)`

## 3.0.4 - 2021-04-14

- Support union types (#185)
- Resolve default cast from parent classes (#189)
- Support default values (#191)

## 3.0.3 - 2021-04-08

- Fix when nested DTO have casted field (#178)

## 3.0.2 - 2021-04-02

- Allow valid DTOs to be passed to caster (#177)

## 3.0.1 - 2021-04-02

- Fix for null values with casters

## 3.0.0 - 2021-04-02

This package now focuses only on object creation by adding easy-to-use casting and data validation functionality. All runtime type checks are gone in favour of the improved type system in PHP 8.

- Require `php:^8.0`
- Removed all runtime type checking functionality, you should use typed properties and a static analysis tool like Psalm or PhpStan
- Removed `Spatie\DataTransferObject\DataTransferObjectCollection`
- Removed `Spatie\DataTransferObject\FlexibleDataTransferObject`, all DTOs are now considered flexible
- Removed runtime immutable DTOs, you should use static analysis instead
- Added `Spatie\DataTransferObject\Validator`
- Added `Spatie\DataTransferObject\Validation\ValidationResult`
- Added `#[DefaultCast]`
- Added `#[CastWith]`
- Added `Spatie\DataTransferObject\Caster`
- Added `#[Strict]`

## 2.8.3 - 2021-02-12

- Add support for using `collection` internally

## 2.8.2 - 2021-02-11

This might be a breaking change, but it was required for a bugfix

- Prevent DataTransferObjectCollection from iterating over array copy (#166)

## 2.8.1 - 2021-02-10

- Fix for incorrect return type (#164)

## 2.8.0 - 2021-01-27

- Allow the traversal of collections with string keys

## 2.7.0 - 2021-01-21

- Cast nested collections (#117)

## 2.6.0 - 2020-11-26

- Support PHP 8

## 2.5.0 - 2020-08-28

- Group type errors (#130)

## 2.4.0 - 2020-08-28

- Support for `array<int, string>` syntax (#136)

## 2.3.0 - 2020-08-19

- Add PHPStan extension to support `checkUninitializedProperties: true` (#135)

## 2.2.1 - 2020-05-13

- Validator for typed 7.4 properties (#109)

## 2.2.0 - 2020-05-08

- Add support for typed properties to DTO casting in PHP 7.4

## 2.0.0 - 2020-04-28

- Bump minimum required PHP version to 7.4
- Support for nested immutable DTOs (#86)

## 1.13.3 - 2020-01-29

- Ignore static properties when serializing (#88)

## 1.13.2 - 2020-01-08

- DataTransferObjectError::invalidType : get actual type before mutating $value for the error message (#81)

## 1.13.1 - 2020-01-08

- Improve extendability of DTOs (#80)

## 1.13.0 - 2020-01-08

- Ignore static properties (#82)
- Add `DataTransferObject::arrayOf` (#83)

## 1.12.0 - 2019-12-19

- Improved performance by adding a cache (#79)
- Add `FlexibleDataTransferObject` which allows for unknown properties to be ignored

## 1.11.0 - 2019-11-28 (#71)

- Add `iterable` and `iterable<\Type>` support

## 1.10.0 - 2019-10-16

- Allow a DTO to be constructed without an array (#68)

## 1.9.1 - 2019-10-03

- Improve type error message

## 1.9.0 - 2019-08-30

- Add DataTransferObjectCollection::items()

## 1.8.0 - 2019-03-18

- Support immutability

## 1.7.1 - 2019-02-11

- Fixes #47, allowing empty dto's to be cast to using an empty array.

## 1.7.0 - 2019-02-04

- Nested array DTO casting supported.

## 1.6.6 - 2018-12-04

- Properly support `float`.

## 1.6.5 - 2018-11-20

- Fix uninitialised error with default value.

## 1.6.4 - 2018-11-15

- Don't use `allValues` anymore.

## 1.6.3 - 2018-11-14

- Support nested collections in collections
- Cleanup code

## 1.6.2 - 2018-11-14

- Remove too much magic in nested array casting

## 1.6.1 - 2018-11-14

- Support nested `toArray` in collections.

## 1.6.0 - 2018-11-14

- Support nested `toArray`.

## 1.5.1 - 2018-11-07

- Add strict type declarations

## 1.5.0 - 2018-11-07

- Add auto casting of nested DTOs

## 1.4.0 - 2018-11-05

- Rename to data-transfer-object

## 1.2.0 - 2018-10-30

- Add uninitialized errors.

## 1.1.1 - 2018-10-25

- Support instanceof on interfaces when type checking

## 1.1.0 - 2018-10-24

- proper support for collections of value objects

## 1.0.0 - 2018-10-24

- initial release
