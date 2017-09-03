<?php

namespace ntentan\panie;

/**
 * Description of Container
 *
 * @author ekow
 */
class Container
{

    private $bindings;
    private $singletons = [];

    public function __construct()
    {
        $this->bindings = new Bindings();
    }

    /**
     * 
     * @param string|\ReflectionClass $class
     * @return \ReflectionClass
     */
    private function getResolvedClassName($class)
    {
        $bound = null;
        if ($this->bindings->has($class)) {
            $bound = $this->bindings->get($class);
        } else if (is_string($class) && class_exists($class)) {
            $bound = ['binding' => $class];
        }
        return $bound;
    }

    public function bind($type)
    {
        return $this->bindings->setActiveKey($type);
    }

    public function has($type)
    {
        return $this->bindings->has($type);
    }

    public function setup($bindings)
    {
        $this->bindings->merge($bindings);
    }

    public function resolve($type, $constructorArguments = [])
    {
        if ($type === null) {
            throw new exceptions\ResolutionException("Cannot resolve an empty type");
        }
        $resolvedClass = $this->getResolvedClassName($type);
        if ($resolvedClass['binding'] === null) {
            throw new exceptions\ResolutionException("Could not resolve dependency $type");
        }
        if ($resolvedClass['singleton'] ?? false) {
            $instance = $this->getSingletonInstance($type, $resolvedClass['binding'], $constructorArguments);
        } else {
            $instance = $this->getInstance($resolvedClass['binding'], $constructorArguments);
        }
        
        foreach($resolvedClass['calls'] ?? [] as $method => $parameters) {
            $method = new \ReflectionMethod($instance, $method);
            $method->invokeArgs($instance, $this->getMethodArguments($method, $parameters));
        }
        
        return $instance;
    }

    private function resolveArgument($argument, $class)
    {
        if($class && is_string($argument)) {
            return $this->resolve($argument);
        }
        return $argument;
    }

    private function getMethodArguments($method, $methodArguments)
    {
        $argumentValues = [];
        $parameters = $method->getParameters();
        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();
            $className = $class ? $class->getName() : null;
            if (isset($methodArguments[$parameter->getName()])) {
                $argumentValues[] = $this->resolveArgument($methodArguments[$parameter->getName()], $className);
            } else {
                $argumentValues[] = $className ? $this->resolve($className) : null;
            }
        }
        return $argumentValues;
    }

    private function getSingletonInstance($type, $class, $constructorArguments)
    {
        if (!isset($this->singletons[$type])) {
            $this->singletons[$type] = $this->getInstance($class, $constructorArguments);
        }
        return $this->singletons[$type];
    }

    private function getInstance($className, $constructorArguments = [])
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
