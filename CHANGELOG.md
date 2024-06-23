# CHANGELOG

## Uncommited
### Added
- An `Inject` attribute to force the injection of values all over the class. Values can now be injected into class properties, constructor arguments, and method arguments alike, regardless of access level (public, private, or protected).
- A `provide()` ... `with()` construct to the binder. This allows injection of arbitrary constructor values, including primitively typed ones.

### Removed
- The `withArgs()` method for constructor parameters has been replaced with a the `Inject` attribute.

## v0.7.1 - 2022-12-20
- Fixed bugs with features that were deprecated and removed in PHP 8.2

## v0.7.0 - 2018-12-02
- First release with a proper changelog
- Adds the `withArgs()` method for constructor parameters
