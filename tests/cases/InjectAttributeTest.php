<?php

namespace ntentan\panie\tests\cases;

use ntentan\panie\Container;

use ntentan\panie\tests\classes\TestInterface;
use ntentan\panie\tests\classes\TestClass;
use ntentan\panie\tests\classes\InjectableProperties;
use ntentan\panie\tests\classes\InjectableMethods;

use PHPUnit\Framework\TestCase;

/**
 * Tests the injection of the 
 *
 * @author ekow
 */
class InjectAttributeTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function setup() : void
    {
        $this->container = new Container();
        $this->container->bind(TestInterface::class)->to(TestClass::class);
    }
    
    public function testPropertyBinding() 
    {
        $object = $this->container->get(InjectableProperties::class);
        $this->assertInstanceOf(TestClass::class, $object->publicInjected);
        $this->assertInstanceOf(TestClass::class, $object->getPrivate());
        $this->assertInstanceOf(TestClass::class, $object->getProtected());
    }
    
    public function testMethodCalling()
    {
        $object = $this->container->get(InjectableMethods::class);
        $this->assertInstanceOf(TestClass::class, $object->publicInjected);
        $this->assertInstanceOf(TestClass::class, $object->getPrivate());
        $this->assertInstanceOf(TestClass::class, $object->getProtected());        
    }
}
