<?php

namespace ntentan\panie;

use ntentan\panie\exceptions\InjectionException;
use ntentan\panie\exceptions\ResolutionException;
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
     * Keep track of the resolution path to help with debugging when there are failed resolutions.
     * @var array
     */
    private array $resolutionPath = [];

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
     * @param string $id
     * @return bool
     */
    public function has($id) : bool
    {
        return $this->bindings->has($id);
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
     * @param ?string $name
     * @return mixed
     */
    private function resolve(string $type, ?string $name = null, array $parameterBindings = []) : mixed
    {
        $this->resolutionPath[] = $type;
        $type = $name === null ? $type : "$$name:$type";
        $resolvedClass = $this->getResolvedBinding($type);
        if ($resolvedClass === null || $resolvedClass['binding'] === null) {
            return null;
        }
        if ($resolvedClass['singleton'] ?? false) {
            if (!empty($parameterBindings)) {
                throw new InjectionException("Cannot perform inline injections with singletons");
            }
            $instance = $this->getSingletonInstance($type, $resolvedClass['binding']);
        } else {
            $instance = $this->getInstance($resolvedClass['binding'], $parameterBindings);
        }

        foreach($resolvedClass['calls'] ?? [] as $call) {
            $method = new \ReflectionMethod($instance, $call[0]);
            $method->invokeArgs($instance, $this->getMethodArguments($method, $call[1]));
        }
        array_pop($this->resolutionPath);

        return $instance;
    }

    /**
     * Returns an object of the type requested, provided the container is adequately configured.
     *
     * @param string $id
     * @return mixed
     * @throws exceptions\ResolutionException
     */
    public function get($id)
    {
        $value = $this->resolve($id);
        if ($value === null) {
            throw new exceptions\ResolutionException("Could not resolve dependency of type [$id] for request: " . implode('->', $this->resolutionPath));
        }
        return $value;
    }

    private function getExplicitConstructorArguments(\ReflectionParameter $parameter): mixed
    {
        $arguments = [];
        $attributes = $parameter->getAttributes();
        if (count($attributes) > 0) {
            foreach ($attributes as $attribute) {
                if (is_a($attribute->getName(), ConstructWith::class, true)) {
                    $instance = $attribute->newInstance();
                    $arguments[$instance->argument] = $instance->value;
                }
            }
        }
        return $arguments;
    }

    /**
     * Resolves all the arguments of a method or constructor.
     *
     * @param \ReflectionMethod $method
     * @param array $localBindings
     * @return array
     * @throws InjectionException
     */
    public function getMethodArguments(\ReflectionMethod $method, array $localBindings = []) : array
    {
        $argumentValues = [];
        $parameters = $method->getParameters();
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            $argumentName = $parameter->getName();

            if ($parameter->isVariadic()) {
                continue;
            }
            
            if ($type instanceof \ReflectionNamedType) {
                $className = $type->getName();
                if (isset($localBindings[$argumentName])) {
                    $argumentValue = $localBindings[$argumentName];
                } else {
                    $parameterBindings = $this->getExplicitConstructorArguments($parameter);
                    $argumentValue = $this->bindings->has("$$argumentName:$className")
                        ? $this->resolve($className, $argumentName, $parameterBindings)
                        : $this->resolve($className, parameterBindings: $parameterBindings);
                    if ($argumentValue === null && $parameter->isDefaultValueAvailable()) {
                        $argumentValue = $parameter->getDefaultValue();
                    } else if ($argumentValue === null && !$parameter->allowsNull()) {
                        throw new exceptions\InjectionException(
                        "Could not resolve a value for [\${$argumentName}] of type [{$className}] for {$method->getDeclaringClass()->getName()}::{$method->getName()}()."
                            . "Resolution hierarchy: " . implode(" > ", $this->resolutionPath)
                        );
                    }
                }
                $argumentValues[] = $argumentValue;
            } else if ($parameter->isDefaultValueAvailable()) {
                $argumentValues[] = $parameter->getDefaultValue();
            } else {
                throw new exceptions\InjectionException("Could not resolve a value for {$argumentName} of type {$type} for {$method->getDeclaringClass()->getName()}{$method->getName()}");
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
            $this->singletons[$type] = $this->getInstance($class, []);
        }
        return $this->singletons[$type];
    }

    /**
     * Returns an instance of a class.
     *
     * @param string|callable $class
     * @param array $parameterBindings
     * @return mixed
     * @throws \ReflectionException
     * @throws ResolutionException
     */
    private function getInstance(string|callable $class, array $parameterBindings = []): mixed
    {
        // If the class is a function call it as a factory.
        if (is_callable($class)) {
            return $class($this);
        }
        $reflection = new \ReflectionClass($class);
        if ($reflection->isAbstract()) {
            throw new exceptions\ResolutionException(
            "Abstract class {$reflection->getName()} cannot be instantiated. "
                . "Please provide a binding to an implementation. Resolution hierarchy: "
                . implode('>', $this->resolutionPath)
            );
        }
        $constructor = $reflection->getConstructor();
        return $reflection->newInstanceArgs($constructor ? $this->getMethodArguments($constructor, $parameterBindings) : []);
    }
}
