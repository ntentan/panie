<?php
namespace ntentan\panie\tests\cases;

use ntentan\panie\tests\classes\TestInterface;
use ntentan\panie\tests\classes\TestClass;
use ntentan\panie\InjectionContainer;
use ntentan\panie\tests\classes\Constructor;
use ntentan\panie\tests\classes\AbstractClass;

class BindingTest extends \PHPUnit_Framework_TestCase
{
    public function testBinding()
    {
        InjectionContainer::bind(TestInterface::class)->to(TestClass::class);
        $object = InjectionContainer::resolve(TestInterface::class);
        $this->assertInstanceOf(TestClass::class, $object);
        $this->assertInstanceOf(TestInterface::class, $object);
    }
    
    public function testConstructorBinding()
    {
        InjectionContainer::bind(TestInterface::class)->to(TestClass::class);
        $object = InjectionContainer::resolve(Constructor::class);
        $this->assertInstanceOf(Constructor::class, $object);
        $this->assertInstanceOf(TestClass::class, $object->getClass());
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertInstanceOf(TestInterface::class, $object->getInterface());
    }
    
    public function testSingleton()
    {
        InjectionContainer::bind(TestInterface::class)->to(TestClass::class);
        $object1 = InjectionContainer::singleton(TestInterface::class);
        $object2 = InjectionContainer::singleton(TestInterface::class);
        $object1->setValue(2);
        $this->assertEquals(2, $object1->getValue());
        $this->assertEquals(2, $object2->getValue());
    }
    
    /**
     * @expectedException \ntentan\panie\exceptions\ResolutionException
     */
    public function testResolutionException()
    {
        InjectionContainer::resolve('UnboundClass');
    }
    
    /**
     * @expectedException \ntentan\panie\exceptions\ResolutionException
     */
    public function testAbstractResolutionException()
    {
        InjectionContainer::resolve(AbstractClass::class);
    }    
    
    public function testMixedConstructorNulls()
    {
        InjectionContainer::bind(TestInterface::class)->to(TestClass::class);
        $object = InjectionContainer::resolve(\ntentan\panie\tests\classes\MixedConstructor::class);
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertNull($object->getString());
        $this->assertNull($object->getNumber());
    }
    
    public function testMixedConstructor()
    {
        InjectionContainer::bind(TestInterface::class)->to(TestClass::class);
        $object = InjectionContainer::resolve(
            \ntentan\panie\tests\classes\MixedConstructor::class,
            ['string' => 'It is a string', 'number' => 2000]
        );
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertEquals('It is a string', $object->getString());
        $this->assertEquals(2000, $object->getNumber());
    }
    
    
    public function tearDown()
    {
        InjectionContainer::reset();
    }
}