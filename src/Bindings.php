<?php

namespace ntentan\panie;

class Bindings {

    private $bindings = [];
    private $activeKey;

    public function setActiveKey($activeKey) {
        $this->activeKey = $activeKey;
        return $this;
    }

    public function to($value) {
        $this->bindings[$this->activeKey] = ['class' => $value];
        return $this;
    }

    public function get($key) {
        return $this->bindings[$key];
    }

    public function has($key) {
        return isset($this->bindings[$key]);
    }
    
    public function asSingleton() {
        $this->bindings[$this->activeKey]['singleton'] = true;
    }
    
    public function remove($type) {
        unset($this->bindings[$type]);
    }

}
