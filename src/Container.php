<?php

namespace ntentan\panie;

use Psr\Container\ContainerInterface;

/**
 * Container class through which dependencies are defined and resolved.
 *
 * @author ekow
 */
class Container implements ContainerInterface
{

    /**
     * Holds all bindings defined.
     * @var Bindings 
     */
    private $bindings;
    
    /**
     * Holds instances of all singletons.
     * @var array
     */
    private $singletons = [];

    public function __construct()
    {
        $this->bindings = new Bindings();
    }

    /**
     * Resolves names of items requested from the container to their correct binding definition.
     * 
     * @param string $class
     * @return array|null The name of the class detected or null
     */
    private function getResolvedBinding(string $class)
    {
        $bound = null;
        if ($this->bindings->has($class)) {
            $bound = $this->bindings->get($class);
        } else if (is_string($class) && class_exists($class)) {
            $bound = ['binding' => $class];
        }
        return $bound;
    }

    /**
     * Starts the process of defining a binding.
     * 
     * @param string $type
     * @return \ntentan\panie\Bindings
     */
    public function bind(string $type) : Bindings
    {
        return $this->bindings->setActiveKey($type);
    }

    /**
     * Returns true if type is found in container otherwise it returns false.
     * 
     * @param string $type
     * @return bool
     */
    public function has($type) : bool
    {
        return $this->bindings->has($type);
    }

    /**
     * Pass an array of bindings to the container.
     * 
     * @param array $bindings
     */
    public function setup($bindings) : void
    {
        $this->bindings->merge($bindings);
    }

    /**
     * Resolves a type and returns an instance of an object of the requested type.
     * Optional constructor arguments could be provided to be used in initializing the object. This method throws a 
     * ResolutionException in cases where the type could not be resolved.
     * 
     * @param string $type
     * @param array $constructorArguments
     * @return mixed
     * @throws exceptions\ResolutionException
     */
    public function resolve(string $type, array $constructorArguments = [])
    {
        $resolvedClass = $this->getResolvedBinding($type);
        if ($resolvedClass['binding'] === null) {
            throw new exceptions\ResolutionException("Could not resolve dependency of type [$type]");
        }
        if ($resolvedClass['singleton'] ?? false) {
            $instance = $this->getSingletonInstance($type, $resolvedClass['binding'], $constructorArguments);
        } else {
            $instance = $this->getInstance($resolvedClass['binding'], $constructorArguments);
        }
        
        foreach($resolvedClass['calls'] ?? [] as $calls) {
            $method = new \ReflectionMethod($instance, $calls[0]);
            $method->invokeArgs($instance, $this->getMethodArguments($method, $calls[1]));
        }
        
        return $instance;
    }

    /**
     * Returns an instance of the type requested if this type (which was requested) is defined in the container.
     * 
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        return $this->resolve($type);
    }

    /**
     * Resolves an argument for a method or constructor.
     * If the argument passed is a string and the type hint of the argument points to an object, the string passed is
     * assumed to be a class binding and it is resolved.
     * 
     * @param mixed $argument
     * @param string $class
     * @return mixed
     */
    private function resolveArgument($argument, $class)
    {
        if($class && is_string($argument)) {
            return $this->resolve($argument);
        }
        return $argument;
    }

    /**
     * Resolves all the arguments of a method or constructor.
     * 
     * @param \ReflectionMethod $method
     * @param array $methodArguments
     * @return array
     */
    private function getMethodArguments(\ReflectionMethod $method, array $methodArguments) : array
    {
        $argumentValues = [];
        $parameters = $method->getParameters();
        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();
            $className = $class ? $class->getName() : null;
            if (isset($methodArguments[$parameter->getName()])) {
                $argumentValues[] = $this->resolveArgument($methodArguments[$parameter->getName()], $className);
            } else {
                $argumentValues[] = $className ? $this->resolve($className) :
                    ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
            }
        }
        return $argumentValues;
    }

    /**
     * Returns a singleton of a given bound type.
     * 
     * @param string $type
     * @param mixed $class
     * @param array $constructorArguments
     * @return mixed
     */
    private function getSingletonInstance(string $type, $class, array $constructorArguments)
    {
        if (!isset($this->singletons[$type])) {
            $this->singletons[$type] = $this->getInstance($class, $constructorArguments);
        }
        return $this->singletons[$type];
    }

    /**
     * Returns an instance of a class.
     * 
     * @param string|closure $className
     * @param array $constructorArguments
     * @return mixed
     * @throws exceptions\ResolutionException
     */
    private function getInstance($className, array $constructorArguments = [])
    {
        if (is_callable($className)) {
            return $className($this);
        }
        $reflection = new \ReflectionClass($className);
        if ($reflection->isAbstract()) {
            throw new exceptions\ResolutionException(
            "Abstract class {$reflection->getName()} cannot be instantiated. "
            . "Please provide a binding to an implementation."
            );
        }
        $constructor = $reflection->getConstructor();
        return $reflection->newInstanceArgs($constructor ? $this->getMethodArguments($constructor, $constructorArguments) : []);
    }
}
