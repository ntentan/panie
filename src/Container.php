<?php

namespace ntentan\panie;

/**
 * Description of Container
 *
 * @author ekow
 */
class Container
{
    private $bindings = [];
    
    /**
     * 
     * @param string|\ReflectionClass $class
     * @return \ReflectionClass
     */
    private static function getReflectionClass($class)
    {
        if(is_a($class, '\ReflectionClass')) {
            return $class;
        } else if(is_string($class) && class_exists($class)) {
            return new \ReflectionClass($class);
        } else {
            return null;
        }
    }
    
    
    public static function resolve($class, ...$args)
    {
        $reflection = self::getReflectionClass($class);
        if($reflection === null) throw new ContainerException ("Could not resolve class $class");
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        $instanceParameters = [];
        foreach($parameters as $parameter) {
            $instanceParameters[] = self::resolve($parameter->getClass());
        }
        return $reflection->newInstanceArgs($instanceParameters);
    }
    
    public static function reset()
    {
        self::$bindings = [];
    }
}
