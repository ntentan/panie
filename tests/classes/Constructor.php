<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ntentan\panie\tests\classes;

/**
 * Description of Constructor
 *
 * @author ekow
 */
class Constructor
{
    private $testClass;
    private $testInterface;
    
    public function __construct(TestClass $class, TestInterface $interface)
    {
        $this->testClass = $class;
        $this->testInterface = $interface;
    }
    
    public function getClass()
    {
        return $this->testClass;
    }
    
    public function getInterface()
    {
        return $this->testInterface;
    }
}
