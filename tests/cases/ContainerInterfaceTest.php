<?php

namespace ntentan\panie\tests\cases;
use ntentan\panie\Container;
use ntentan\panie\tests\classes\TestClass;
use ntentan\panie\tests\classes\TestInterface;
use PHPUnit\Framework\TestCase;


class ContainerInterfaceTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->container->setup([
            TestInterface::class => TestClass::class
        ]);
    }

    public function testHas()
    {
        $this->assertEquals(true, $this->container->has(TestInterface::class));
    }

    public function testGet()
    {
        $this->assertInstanceOf(TestClass::class, $this->container->get(TestInterface::class));
    }
}
