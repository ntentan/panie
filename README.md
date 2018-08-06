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
In cases where we want to provide specific wiring, we can configure the container by passing a associative array of options to use when resolving certain class types.

As a example, in the case where our `Foo` class takes a `BarInterface` iterface and we specifically want the `BarImplementation` class, we can wire the container as follows:

````php
<?php
require "vendor/autoload.php";
$container = new ntentan\panie\Container();
$container->bind(BarInterface::class)->to(BarImplementation::class);
$fooInstance = $container->get(Foo::class);
````

The container can also take a factory function that returns an instance of the required types. 

````php
<?php
require "vendor/autoload.php";
$container = new ntentan\panie\Container();
$container->bind(BarInterface::class)->to(function(){
    return new BarImplementation();
});
````

You can also make the container maintain an internal singleton of a particular service by specifying the singleton flag when binding types.

````php
<?php
require "vendor/autoload.php";
$container = new ntentan\panie\Container();
$container->bind(BarInterface::class)->to(BarImplementation::class)->asSingleton();
$fooInstance = $container->get(Foo::class);
````

Apart from the `bind` and `to` functions, the container has a `setup` function that takes an associative array with wiring info for the container. In this case, a possible wiring could be ...

````php
$container->setup([
    BarInterface::class => BarImplementation::class
]);
````

... or for singletons and factories ...

````php
$container->setup([
    BarInterface::class => [function(){ ... }, 'singleton' => true]
]);
````

