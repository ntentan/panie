<?php

namespace ntentan\panie;

/**
 * Description of Container
 *
 * @author ekow
 */
class Container {

    private $bindings;
    private $singletons = [];

    public function __construct() {
        $this->bindings = new Bindings();
    }

    /**
     * 
     * @param string|\ReflectionClass $class
     * @return \ReflectionClass
     */
    public function getResolvedClassName($class) {
        $bound = null;
        if ($this->bindings->has($class)) {
            $bound = $this->bindings->get($class);
        } else if (is_string($class) && class_exists($class)) {
            $bound = ['class' => $class];
        }
        if ($bound === null) {
            throw new exceptions\ResolutionException("Could not resolve dependency $class");
        }        
        return $bound;
    }

    public function bind($lose) {
        return $this->bindings->setActiveKey($lose);
    }

    public function singleton($type, $constructorArguments = []) {
        $resolvedClass = $this->getResolvedClassName($type)['class'];
        return $this->getSingletonInstance($type, $resolvedClass, $constructorArguments);
    }
    
    private function getSingletonInstance($type, $class,  $constructorArguments) {
        if (!isset($this->singletons[$type])) {
            $this->singletons[$type] = $this->getInstance($class, $constructorArguments);
        }
        return $this->singletons[$type];        
    }
    
    public function resolve($type, $constructorArguments = []) {
        $resolvedClass = $this->getResolvedClassName($type);
        if(isset($resolvedClass['singleton'])) {
            return $this->getSingletonInstance($type, $resolvedClass['class'], $constructorArguments);
        } else {
            return $this->getInstance($resolvedClass['class'], $constructorArguments);
        }
    }

    public function getInstance($className, $constructorArguments = []) {
        
        $reflection = new \ReflectionClass($className);
        if ($reflection->isAbstract()) {
            throw new exceptions\ResolutionException(
            "Abstract class {$reflection->getName()} cannot be instantiated. "
            . "Please provide a binding to an implementation."
            );
        }
        $constructor = $reflection->getConstructor();
        $instanceParameters = [];

        if ($constructor != null) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $class = $parameter->getClass();
                if (isset($constructorArguments[$parameter->getName()])) {
                    $instanceParameters[] = $constructorArguments[$parameter->getName()];
                } else {
                    $instanceParameters[] = $class ? $this->resolve($class->getName()) : null;
                }
            }
        }
        return $reflection->newInstanceArgs($instanceParameters);
    }

}
