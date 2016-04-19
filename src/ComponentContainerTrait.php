<?php

namespace ntentan\panie;

/**
 * Description of ComponentContainer
 *
 * @author ekow
 */
trait ComponentContainerTrait
{
    protected $loadedComponents = [];
    private static $resolverParameters = [];
    
    public static function setComponentResolverParameters($resolverParameters)
    {
        self::$resolverParameters = $resolverParameters;
    }
    
    protected function getComponentInstance($component)
    {
        if(!isset($this->loadedComponents[$component])) {
            $className = InjectionContainer::resolve(ComponentResolverInterface::class)
                ->getComponentClass($component, self::$resolverParameters);
            $componentInstance = InjectionContainer::resolve($className);
            $this->loadedComponents[$component] = $componentInstance;
        }
        return $this->loadedComponents[$component];
    }
}
