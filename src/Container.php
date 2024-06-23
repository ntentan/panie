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
     * Holds all defined class bindings.
     *
     * @var Bindings 
     */
    private Bindings $bindings;
    
    /**
     * Holds instances of all singletons.
     *
     * @var array
     */
    private array $singletons = [];
    
    /**
     * A flag to determine whether to inject objects into symbols with attributes.
     * 
     * @var bool
     */
    private bool $useAttribute;

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
    private function getResolvedBinding(string $class) : ?array
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
     * This method selects an active binding for the internal bindings object.
     * 
     * @param string $type
     * @return \ntentan\panie\Bindings
     */
    public function bind(string $type) : Bindings
    {
        return $this->bindings->setActiveKey($type);
    }
    
    public function provide(string $type, string $name): Bindings
    {
        return $this->bindings->provide($type, $name);
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
    public function setup(array $bindings) : Container
    {
        $this->bindings->merge($bindings);
        return $this;
    }

    /**
     * Resolves a type and returns an instance of an object of the requested type.
     * Optional constructor arguments could be provided to be used in initializing the object. This method throws a 
     * ResolutionException in cases where the type could not be resolved.
     *
     * @todo Deprecate the use of the constructor arguments sometime soon
     * @param string $type
     * @param string $name
     * @param array $constructorArguments
     * @return mixed
     * @throws exceptions\ResolutionException
     */
    private function resolve(string $type, string $name = null) : mixed
    {
        $resolvedClass = $this->getResolvedBinding($name === null ? $type : "$$name:$type");
        if ($resolvedClass === null || $resolvedClass['binding'] === null) {
            throw new exceptions\ResolutionException("Could not resolve dependency of type [$type]" . ($name !== null ? " for [$name]." : ""));
        }
        if ($resolvedClass['singleton'] ?? false) {
            $instance = $this->getSingletonInstance($type, $resolvedClass['binding']);
        } else {
            $instance = $this->getInstance($resolvedClass['binding']);
        }
        
        return $instance;
    }

    /**
     * Returns an object of the type requested, provided the container is adequately configured.
     *
     * @param string $type
     * @return mixed
     * @throws exceptions\ResolutionException
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
     * @throws exceptions\ResolutionException
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
     * @throws exceptions\ResolutionException
     */
    private function getMethodArguments(\ReflectionMethod $method) : array
    {
        $argumentValues = [];
        $parameters = $method->getParameters();
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType) {                
                $className = $type->getName(); //isBuiltin() ? ; //$class ? $class->getName() : null;
                $argumentName = $parameter->getName();
                $argumentValues[] = $this->bindings->has("$$argumentName:$className") 
                        ? $this->resolve($className, $argumentName) 
                        : $this->resolve($className);
            } else {
                throw new exceptions\InjectionException("Only properties with single named types can be injected with values.");
            }
        }
        return $argumentValues;
    }

    /**
     * Returns a singleton of a given bound type.
     *
     * @param string $type
     * @param mixed $class
     * @return mixed
     * @throws exceptions\ResolutionException
     */
    private function getSingletonInstance(string $type, $class)
    {
        if (!isset($this->singletons[$type])) {
            $this->singletons[$type] = $this->getInstance($class);
        }
        return $this->singletons[$type];
    }
    
    private function injectAttributed(mixed $instance, \ReflectionClass $reflection): void
    {
        // Properties
        foreach($reflection->getProperties() as $property) {
            if (count($property->getAttributes(Inject::class)) == 0 ) {
                continue;
            }
            $type = $property->getType();
            if ($type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
                $name = $property->getName();
                $property->setValue($instance, 
                    $this->bindings->has("$$name:$typeName") ? $this->resolve($typeName, $name) : $this->resolve($typeName)
                );
            } else {
                throw new exceptions\InjectionException("Only properties with single named types can be injected with values.");
            }
        }
        
        // Methods
        foreach($reflection->getMethods() as $method) {
            if (count($method->getAttributes(Inject::class)) == 0) {
                continue;
            }
            $method->invokeArgs($instance, $this->getMethodArguments($method));
        }
    }

    /**
     * Returns an instance of a class.
     * 
     * @param string|callable $class
     * @param array $constructorArguments
     * @return mixed
     * @throws exceptions\ResolutionException
     */
    private function getInstance(string|callable $class): mixed
    {
        // If the class is a function call it as a factory.
        if (is_callable($class)) {
            return $class($this);
        }
        $reflection = new \ReflectionClass($class);
        if ($reflection->isAbstract()) {
            throw new exceptions\ResolutionException(
            "Abstract class {$reflection->getName()} cannot be instantiated. "
            . "Please provide a binding to an implementation."
            );
        }
        $constructor = $reflection->getConstructor();
        $instance = $reflection->newInstanceArgs($constructor ? $this->getMethodArguments($constructor) : []);
        
        $this->injectAttributed($instance, $reflection);
        
        return $instance;
    }
}
