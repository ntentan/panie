v0.9.2 - 2025-04-13
===================
- Fixes a tiny bug in the resolution of built-in types.

v0.9.1 - 2025-04-05
===================
- Emphasizes nullability of strings in the `resolve` function.

v0.9.0 - 2025-01-18
===================
Removed
-------
- The `Inject` attribute has been removed because it's no longer required.

v0.8.0 - 2024-06-23
===================
Added
-----
- An `Inject` attribute to force the injection of values all over the class. Values can now be injected into class properties, constructor arguments, and method arguments alike, regardless of access level (public, private, or protected).
- A `provide()` ... `with()` construct to the binder. This allows injection of arbitrary constructor values, including primitively typed ones.

Removed
-------
- The `withArgs()` method for constructor parameters has been replaced with a the `Inject` attribute.

v0.7.1 - 2022-12-20
===================
- Fixed bugs with features that were deprecated and removed in PHP 8.2

v0.7.0 - 2018-12-02
===================
- First release with a proper changelog
- Adds the `withArgs()` method for constructor parameters
