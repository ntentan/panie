<?php

namespace ntentan\panie\tests\classes;

/**
 * Description of TestClass
 *
 * @author ekow
 */
class TestClass implements TestInterface
{
    private $value;
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}
