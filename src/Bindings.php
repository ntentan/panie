<?php

namespace ntentan\panie;

/**
 * Holds all the bindings for the dependency injection.
 */
class Bindings
{

    /**
     * An array of all the bindings.
     *
     * @var array
     */
    private $bindings = [];
    
    /**
     * Key of the binding that would be altered with subsequent calls to a bindings object.
     *
     * @var string 
     */
    private $activeKey;

    /**
     * Set the key of the binding to be modified or accessed on subsequent calls.
     *
     * @param $activeKey
     * @return self
     */
    public function setActiveKey(string $activeKey) : self
    {
        $this->activeKey = $activeKey;
        return $this;
    }

    /**
     * Registers a method to be called when an instance is created for the current active binding.
     * This method could be a setter or some other optional initialization method. Parameters for methods registered
     * with call that have type hints are also injected.
     *
     * @param string $method
     * @param array $parameters
     * @return self
     */
    public function call(string $method, array $parameters = []) : self
    {
        $this->bindings[$this->activeKey]['calls'][] = [$method, $parameters];
        return $this;
    }

    /**
     * Provides a concrete class or factory function to which the currently selected binding should be linked.
     *
     * @param mixed $value
     * @return self
     */
    public function to($value)
    {
        if(isset($this->bindings[$this->activeKey])) {
            $this->bindings[$this->activeKey]['binding'] = $value;
        } else {
            $this->bindings[$this->activeKey] = ['binding' => $value, 'calls' => [], 'properties' => []];
        }
        return $this;
    }

    /**
     * Get the configuration of a binding.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->bindings[$key];
    }

    /**
     * Checks if a binding exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->bindings[$key]);
    }

    /**
     * Register the current active binding as a singleton.
     *
     * @param bool $singleton
     */
    public function asSingleton($singleton = true)
    {
        $this->bindings[$this->activeKey]['singleton'] = $singleton;
    }

    /**
     * Setup bindings from an array.
     *
     * @param array $binding
     */
    private function setArrayBinding(array $binding)
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

        $this->asSingleton($binding['singleton'] ?? false);
    }

    /**
     * Merge current bindings with another configuration overwriting conflicting values with the new bindings.
     *
     * @param array $bindings
     */
    public function merge(array $bindings)
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
