<?php

namespace ntentan\panie;

class Bindings
{
    private $bindings = [];
    private $activeKey;
    
    public function setActiveKey($activeKey)
    {
        $this->activeKey = $activeKey;
        return $this;
    }
    
    public function to($value)
    {
        $this->bindings[$this->activeKey] = $value;
    }
    
    public function get($key)
    {
        return $this->bindings[$key];
    }
    
    public function has($key)
    {
        return isset($this->bindings[$key]);
    }
}
