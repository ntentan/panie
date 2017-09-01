<?php

namespace ntentan\panie;

/**
 * Holds all the bindings for the dependency injection.
 */
class Bindings
{

    /**
     * An array of all the bindings.
     * @var array
     */
    private $bindings = [];
    
    /**
     * An active key that would be altered with subsequent calls to the bindings object.
     * @var string 
     */
    private $activeKey;

    public function setActiveKey($activeKey)
    {
        $this->activeKey = $activeKey;
        return $this;
    }

    public function call($method, $parameters = [])
    {
        $this->bindings[$this->activeKey]['calls'][] = ['setter' => $method, 'parameters' => $parameters];
    }

    public function setProperty($property, $binding)
    {
        $this->bindings[$this->activeKey]['sets'][] = ['property' => $property, 'binding' => $binding];
    }

    public function to($value)
    {
        $this->bindings[$this->activeKey] = ['binding' => $value, 'calls'=>[], 'properties' => []];
        return $this;
    }

    public function get($key)
    {
        return $this->bindings[$key];
    }

    public function has($key)
    {
        return isset($this->bindings[$key]);
    }

    public function merge($bindings, $replace = true)
    {
        foreach($bindings as $key => $binding) {
            if(isset($this->bindings[$key]) && !$replace) {
                continue;
            }
            if(is_array($binding)) {
                $this->bindings[$key] = ['binding' => $binding[0]];
                if(isset($binding['singleton'])) {
                    $this->bindings[$key]['singleton'] = $binding['singleton'];
                }
            } else {
                $this->bindings[$key] = ['binding' => $binding];
            }
        }
    }

}
