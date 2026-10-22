# PHP 8.5 deprecations surfacing in dependent suites

Opened: 2026-10-01
Updated: 2026-10-01
Author: agent:sapiens

# PHP 8.5 deprecations surfacing in dependent suites

Opened: 2026-10-01
Updated: 2026-10-01
Author: agent:sapiens

## Context

Reported by the `symfony-api` agent (commit `234b6e9`, machine authentication for the Sapiens app): its tests raise deprecations coming from this package and from `symfony-loader`. It left them alone, not being its files.

## Task

Run this package's suite, and `symfony-api`'s, on PHP 8.5 with deprecations displayed, and fix those originating here.

## Done

- `src/` lints clean on `php:8.5-cli` with `error_reporting=-1`; this package has no tests of its own.
- `symfony-api`'s suite (`wex-php85-intl`, `--display-deprecations`) raised one deprecation from here, not the implicit-nullable pattern: `new ReflectionMethod('Class::method')` with one argument, in `ClassHelper::getChildrenAttributes()`. Replaced by `ReflectionMethod::createFromMethodName()` (cc7fe40). The suite then reports 6 deprecations, none from `php-helpers`.

## Left to the other packages

- `symfony-api`: implicit nullable `$previous` in `MissingRequiredPropertyException`, `ConstraintViolationException`, `ExtraPropertyException`, `FieldValidationException`; `DATE_RFC7231` in `ApiVersionDeprecationSubscriber:54`.
- `symfony-helpers`: implicit nullable `$previous` in `Exception/AbstractException.php:42`.
- No deprecation from `symfony-loader` shows up any more.

## Outcome

- `src/` lints clean on `php:8.5-cli` with `error_reporting=-1`; this package has no tests of its own.
- `symfony-api`'s suite (`wex-php85-intl`, `--display-deprecations`) raised one deprecation from here, not the implicit-nullable pattern: `new ReflectionMethod('Class::method')` with one argument, in `ClassHelper::getChildrenAttributes()`. Replaced by `ReflectionMethod::createFromMethodName()` (cc7fe40). The suite then reports 6 deprecations, none from `php-helpers`.

Left to the other packages:

- `symfony-api`: implicit nullable `$previous` in `MissingRequiredPropertyException`, `ConstraintViolationException`, `ExtraPropertyException`, `FieldValidationException`; `DATE_RFC7231` in `ApiVersionDeprecationSubscriber:54`.
- `symfony-helpers`: implicit nullable `$previous` in `Exception/AbstractException.php:42`.
- No deprecation from `symfony-loader` shows up any more.
