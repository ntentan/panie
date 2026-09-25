# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.11.0] - 2026-09-25

### Added
- A `ConstructWith` attribute to specify explicit constructor arguments for dependencies.
- Detailed resolution hierarchy in `InjectionException` and `ResolutionException` error messages when dependency resolution fails.
- Resolution error reporting with resolution hierarchy when attempting to instantiate an abstract class without a binding.
- Support for resolving nullable typed arguments to `null` when no binding or default value is available.
- PHPStan static analysis configuration (`phpstan.neon`) and integration into the GitHub Actions test workflow.
- Validation for call configurations in `Bindings`, throwing an `InvalidArgumentException` on invalid call entries.

### Changed
- `Container::getMethodArguments()` is now public and accepts local parameter bindings.
- Renamed parameter `$type` to `$id` in `Container::get()` and `Container::has()` to align with PSR-11.
- Added `phpstan/phpstan` to development dependencies and updated dependencies.

## [v0.10.0] - 2026-08-26

### Added
- Support for variadic arguments in class constructors and methods by skipping them during resolution.
- Support for default parameter values when resolving untyped arguments.

## [v0.9.2] - 2025-04-13

### Fixed
- A bug in the resolution of built-in types.

## [v0.9.1] - 2025-04-05

### Changed
- Emphasized nullability of strings in the `resolve` function.

## [v0.9.0] - 2025-01-18

### Removed
- The `Inject` attribute because it is no longer required.

## [v0.8.0] - 2024-06-23

### Added
- An `Inject` attribute to force the injection of values all over the class. Values can now be injected into class properties, constructor arguments, and method arguments alike, regardless of access level (public, private, or protected).
- A `provide()` ... `with()` construct to the binder. This allows injection of arbitrary constructor values, including primitively typed ones.

### Removed
- The `withArgs()` method for constructor parameters (replaced with the `Inject` attribute).

## [v0.7.1] - 2022-12-20

### Fixed
- Bugs with features that were deprecated and removed in PHP 8.2.

## [v0.7.0] - 2018-12-02

### Added
- First release with a proper changelog.
- The `withArgs()` method for constructor parameters.

[Unreleased]: https://github.com/ntentan/panie/compare/v0.11.0...HEAD
[v0.11.0]: https://github.com/ntentan/panie/compare/v0.10.0...v0.11.0
[v0.10.0]: https://github.com/ntentan/panie/compare/v0.9.2...v0.10.0
[v0.9.2]: https://github.com/ntentan/panie/compare/v0.9.1...v0.9.2
[v0.9.1]: https://github.com/ntentan/panie/compare/v0.9.0...v0.9.1
[v0.9.0]: https://github.com/ntentan/panie/compare/v0.8.0...v0.9.0
[v0.8.0]: https://github.com/ntentan/panie/compare/v0.7.1...v0.8.0
[v0.7.1]: https://github.com/ntentan/panie/compare/v0.7.0...v0.7.1
[v0.7.0]: https://github.com/ntentan/panie/releases/tag/v0.7.0
