<?php

namespace ntentan\panie\tests\cases;

use ntentan\panie\Container;
use PHPUnit\Framework\TestCase;
use ntentan\panie\tests\classes\TestClass;
use ntentan\panie\tests\classes\TestInterface;
use ntentan\panie\tests\classes\MixedConstructor;


/**
 * Description of TestProvision
 *
 * @author ekow
 */
class ProvisioningTest extends TestCase
{
    private Container $container;

    public function setup() : void
    {
        $this->container = new Container();
        $this->container
            ->bind(TestInterface::class)->to(TestClass::class)
                ->provide("string", "stringValue")->with(fn() => "string value correctly set")
                ->provide("int", "integerValue")->with(fn() => 240142);
    }
    
    public function testInjection() 
    {
       $object = $this->container->get(MixedConstructor::class);
       $this->assertInstanceOf(TestClass::class, $object->getInterface());
       $this->assertEquals(240142, $object->getNumber());
       $this->assertEquals("string value correctly set", $object->getString());
    }
    
}
