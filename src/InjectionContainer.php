<?php

namespace ntentan\panie;

/**
 * Description of Container
 *
 * @author ekow
 */
class InjectionContainer
{
    private static $bindings = [];
    private static $singletons = [];
    
    /**
     * 
     * @param string|\ReflectionClass $class
     * @return \ReflectionClass
     */
    public static function getResolvedClassName($class, $argument = null)
    {
        if(isset(self::$bindings[$class])) {
            $bound = self::$bindings[$class];
            if(is_string($bound)) {
                return new $bound;
            } elseif (is_callable($bound)) {
                return $bound($argument);
            }
        }
        if(is_string($class) && class_exists($class)) {
            return $class;
        }
        return null;
    }
    
    public static function bind($lose, $concrete)
    {
        self::$bindings[$lose] = $concrete;
    }
    
    public static function singleton($type)
    {
        if(!isset(self::$singletons[$type])) {
            self::$singletons[$type] = self::resolve($type);
        }
        return self::$singletons[$type];
    }
    
    public static function resolve($type)
    {
        $resolvedClass = self::getResolvedClassName($type);
        if($resolvedClass=== null) {
            throw new exceptions\ResolutionException("Could not resolve dependency $type");
        }
        $reflection = new \ReflectionClass($resolvedClass);
        if($reflection->isAbstract()) {
            throw new exceptions\ResolutionException(
                "Abstract class {$reflection->getName()} cannot be instantiated. "
                . "Please provide a binding to an implementation."
            );
        }
        $constructor = $reflection->getConstructor();
        $instanceParameters = [];
        
        if($constructor != null) {
            $parameters = $constructor->getParameters();
            foreach($parameters as $parameter) {
                $class = $parameter->getClass();
                $instanceParameters[] = $class ? self::resolve($class->getName()) : null;
            }            
        }
        return $reflection->newInstanceArgs($instanceParameters);        
    }
    
    
    public static function reset()
    {
        self::$bindings = [];
    }
}
