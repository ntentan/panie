<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ntentan\panie\tests\classes;

/**
 * Description of MixedConstructor
 *
 * @author ekow
 */
class MixedConstructor
{
    private $interface;
    private $string;
    private $number;
    
    public function __construct(TestInterface $interface, $string, $number)
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
