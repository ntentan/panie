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
        $this->bindings[$this->activeKey]['calls'][] = [$method, $parameters];
    }

    public function setProperty($property, $binding)
    {
        $this->bindings[$this->activeKey]['sets'][$property] = $binding;
    }

    public function to($value)
    {
        if(isset($this->bindings[$this->activeKey])) {
            $this->bindings[$this->activeKey]['binding'] = $value;
        } else {
            $this->bindings[$this->activeKey] = ['binding' => $value, 'calls' => [], 'properties' => []];
        }
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

    public function asSingleton($singleton = true)
    {
        $this->bindings[$this->activeKey]['singleton'] = $singleton;
    }    
    
    private function setArrayBinding($binding)
    {
        if(isset($binding[0])) {
            $this->to($binding[0]);
        }
        
        if(isset($binding['calls'])){
            foreach($binding['calls'] as $call => $parameters) {
                if(is_numeric($call)) {
                    $call = $parameters;
                    $parameters = [];
                } 
                $this->call($call, $parameters);
            }
        } else {
            $this->bindings[$this->activeKey]['calls'] = [];
        }
        
        if(isset($binding['sets'])){
            foreach($binding['sets'] as $property => $parameters) {
                if(is_numeric($property)) {
                    $property = $parameters;
                    $parameters = [];
                } 
                $this->setProperty($property, $parameters);
            }
        } else {
            $this->bindings[$this->activeKey]['sets'] = [];
        }

        $this->asSingleton($binding['singleton'] ?? false);
    }

    public function merge($bindings)
    {
        foreach($bindings as $key => $binding) {
            $this->activeKey = $key;
            if(is_array($binding)) {
                $this->setArrayBinding($binding);
            } else {
                $this->bindings[$key] = ['binding' => $binding, 'calls' => [], 'properties' => []];
            }
        }
    }

}
