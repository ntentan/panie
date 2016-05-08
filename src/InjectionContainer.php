<?php

namespace ntentan\panie;

/**
 * Description of Container
 *
 * @author ekow
 */
class InjectionContainer
{
    private static $bindings;
    private static $singletons = [];
    
    /**
     * 
     * @param string|\ReflectionClass $class
     * @return \ReflectionClass
     */
    public static function getResolvedClassName($class)
    {
        $bound = null;
        if(self::getBindings()->has($class)) {
            $bound = self::$bindings->get($class);
        }
        else if(is_string($class) && class_exists($class)) {
            $bound = $class;
        }
        return $bound;
    }
    
    private static function getBindings() 
    {
        if(!self::$bindings) self::$bindings = new Bindings ();
        return self::$bindings;
    }
    
    public static function bind($lose)
    {
        return self::getBindings()->setActiveKey($lose);
    }
    
    public static function singleton($type, $constructorArguments = [])
    {
        if(!isset(self::$singletons[$type])) {
            self::$singletons[$type] = self::resolve($type, $constructorArguments);
        }
        return self::$singletons[$type];
    }
    
    public static function resolve($type, $constructorArguments = [])
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
                if(isset($constructorArguments[$parameter->getName()])) {
                    $instanceParameters[] = $constructorArguments[$parameter->getName()];
                } else {
                    $instanceParameters[] = $class ? self::resolve($class->getName()) : null;
                }
            }            
        }
        return $reflection->newInstanceArgs($instanceParameters);        
    }
    
    public static function reset()
    {
        self::resetBindings();
        self::resetSingletons();
    }
    
    public static function resetBindings()
    {
        self::$bindings = null;
    }
    
    public static function resetSingletons()
    {
        self::$singletons = [];
    }
}
