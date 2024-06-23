<?php
namespace ntentan\panie\tests\cases;

use ntentan\panie\tests\classes\SettersAndProperties;
use ntentan\panie\tests\classes\TestInterface;
use ntentan\panie\tests\classes\TestClass;
use ntentan\panie\Container;
use ntentan\panie\tests\classes\Constructor;
use ntentan\panie\tests\classes\AbstractClass;
use PHPUnit\Framework\TestCase;
use ntentan\panie\exceptions\ResolutionException;


class PanieTest extends TestCase
{
    /**
     * @var Container
     */
    private Container $container;

    public function setup() : void
    {
        $this->container = new Container();
    }
        
    public function testBinding()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->get(TestInterface::class);
        $this->assertInstanceOf(TestClass::class, $object);
        $this->assertInstanceOf(TestInterface::class, $object);
        $otherobject = $this->container->get(TestInterface::class);
        $object->setValue(2);
        $this->assertEquals(2, $object->getValue());
        $this->assertNotEquals(2, $otherobject->getValue());
    }

    public function testConstructorBinding()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->get(Constructor::class);
        $this->assertInstanceOf(Constructor::class, $object);
        $this->assertInstanceOf(TestClass::class, $object->getClass());
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertInstanceOf(TestInterface::class, $object->getInterface());
    }

    public function testSingleton()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class)->asSingleton();
        $object1 = $this->container->get(TestInterface::class);
        $object2 = $this->container->get(TestInterface::class);
        $object1->setValue(2);
        $this->assertEquals(2, $object1->getValue());
        $this->assertEquals(2, $object2->getValue());
    }    

    public function testResolutionException()
    {
        $this->expectException(ResolutionException::class);
        $this->container->get('UnboundClass');
    }

    public function testAbstractResolutionException()
    {
        $this->expectException(ResolutionException::class);
        $this->container->get(AbstractClass::class);
    }

    public function testHas()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $this->assertEquals(false, $this->container->has(Unknown::class));
        $this->assertEquals(true, $this->container->has(TestInterface::class));
    }

    public function testSetup()
    {
        $this->container->setup([TestInterface::class => TestClass::class]);
        $object = $this->container->get(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $object);
    }

    public function testSetupSingleton()
    {
        $this->container->setup([TestInterface::class=>[TestClass::class, 'singleton' => true]]);
        $object1 = $this->container->get(TestInterface::class);
        $object2 = $this->container->get(TestInterface::class);
        $object1->setValue(2);
        $this->assertEquals(2, $object1->getValue());
        $this->assertEquals(2, $object2->getValue());
    }   
    
    public function testEmptyResolves()
    {
        $this->expectException(ResolutionException::class);
        $this->container->get(Unknown::class);
    }  
    
    public function testUnboundClass()
    {
        $object = $this->container->get(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $object);
    }
    
    public function testFactories()
    {
        $this->container->bind(TestInterface::class)->to(function($container){
            $this->assertInstanceOf(Container::class, $container);
            return new TestClass();
        });
        $object = $this->container->get(TestInterface::class);
        $this->assertInstanceOf(TestClass::class, $object);        
    }

    public function testOverwrite()
    {
        $this->container->bind('some_service')->to(SettersAndProperties::class);
        $this->assertInstanceOf(SettersAndProperties::class, $this->container->get('some_service'));
        $this->container->bind('some_service')->to(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $this->container->get('some_service'));
    }
}
