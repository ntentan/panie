<?php

namespace ntentan\panie\tests\classes;

/**
 * Description of MixedConstructor
 *
 * @author ekow
 */
class MixedConstructor
{
    private TestInterface $interface;
    private string $string;
    private int $number;
    
    public function __construct(TestInterface $interface, string $string, int $number)
    {
        $this->interface = $interface;
        $this->string = $string;
        $this->number = $number;
    }
    
    public function getInterface()
    {
        return $this->interface;
    }
    
    public function getString()
    {
        return $this->string;
    }
    
    public function getNumber()
    {
        return $this->number;
    }
}
