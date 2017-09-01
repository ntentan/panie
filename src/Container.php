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
    public function getResolvedClassName($class)
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

    public function setup($bindings, $replace = true)
    {
        $this->bindings->merge($bindings, $replace);
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
        $instance = $this->getInstance($resolvedClass['binding'], $constructorArguments);
        return $instance;
    }

    private function getConstructorArguments($constructor, $constructorArguments)
    {
        $argumentValues = [];
        $parameters = $constructor->getParameters();
        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();
            $className = $class ? $class->getName() : null;
            if (isset($constructorArguments[$parameter->getName()])) {
                $argumentValues[] = $constructorArguments[$parameter->getName()];
            } else if ($className == self::class) {
                $argumentValues[] = $this;
            } else {
                $argumentValues[] = $className ? $this->resolve($className) : null;
            }
        }
        return $argumentValues;
    }

    public function getInstance($className, $constructorArguments = [])
    {
        if (is_callable($className)) {
            return $className($this);
        }
        if (is_object($className)) {
            return $className;
        }
        $reflection = new \ReflectionClass($className);
        if ($reflection->isAbstract()) {
            throw new exceptions\ResolutionException(
            "Abstract class {$reflection->getName()} cannot be instantiated. "
            . "Please provide a binding to an implementation."
            );
        }
        $constructor = $reflection->getConstructor();
        return $reflection->newInstanceArgs($constructor ? $this->getConstructorArguments($constructor, $constructorArguments) : []);
    }
}
