<?php

namespace ntentan\panie;

/**
 * Description of ComponentContainer
 *
 * @author ekow
 */
trait ComponentContainerTrait {

    protected $loadedComponents = [];
    private static $resolverParameters = [];

    public static function setComponentResolverParameters($resolverParameters) {
        self::$resolverParameters = $resolverParameters;
    }

    protected function getComponentInstance($container, $component) {
        if (!isset($this->loadedComponents[$component])) {
            $className = $container->resolve(ComponentResolverInterface::class)
                    ->getComponentClassName($component, self::$resolverParameters);
            $componentInstance = $container->resolve($className);
            $this->loadedComponents[$component] = $componentInstance;
        }
        return $this->loadedComponents[$component];
    }

}
