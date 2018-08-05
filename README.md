Panie Injection Container
=========================
[![Build Status](https://travis-ci.org/ntentan/panie.svg)](https://travis-ci.org/ntentan/atiaa)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ntentan/panie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ntentan/panie/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ntentan/panie/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ntentan/panie/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ntentan/panie/version.svg)](https://packagist.org/packages/ntentan/panie)
[![Total Downloads](https://poser.pugx.org/ntentan/panie/downloads.svg)](https://packagist.org/packages/ntentan/panie)

A PSR-Container compatible dependency injection container built for use in the ntentan framework. However, just like the other components in the framework, ntentan is not required for panie to work. Panie can be used as a dependency injection container in its own right.

Usage
-----
To create an instance of the container we use:
````php
<?php
require "vendor/autoload.php";
$container = new ntentan\panie\Container();
$fooInstance = $container->resolve(Foo::class);
````
Panie will create an instance of the `Foo` class and autowire the type hinted dependencies if our `Foo` class is defined as follows:

````php
<?php
class Foo
{
    private $bar;

    public function __constructor(Bar $bar) {
        $this->bar = $bar;
    }
}
````

### Configuring

