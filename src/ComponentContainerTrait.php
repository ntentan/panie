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
    
    private static $resolver;
    
    public function getComponent($component)
    {
        if(isset($this->loadedComponents[$component])) {
            return $this->loadedComponents[$component];
        }
    }
    
    protected function loadComponent($component, $params = null)
    {
        $resolver = self::$resolver;
        $className = $resolver($component);
        $componentInstance = new $className($params);
        $this->loadedComponents[Text::camelize($component)] = $componentInstance;
        return $componentInstance;
    }
}
