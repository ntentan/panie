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
    private $container;

    public function setup() : void
    {
        $this->container = new Container();
    }
        
    public function testBinding()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->resolve(TestInterface::class);
        $this->assertInstanceOf(TestClass::class, $object);
        $this->assertInstanceOf(TestInterface::class, $object);
        $otherobject = $this->container->resolve(TestInterface::class);
        $object->setValue(2);
        $this->assertEquals(2, $object->getValue());
        $this->assertNotEquals(2, $otherobject->getValue());
    }

    public function testConstructorBinding()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->resolve(Constructor::class);
        $this->assertInstanceOf(Constructor::class, $object);
        $this->assertInstanceOf(TestClass::class, $object->getClass());
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertInstanceOf(TestInterface::class, $object->getInterface());
    }

    public function testSingleton()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class)->asSingleton();
        $object1 = $this->container->resolve(TestInterface::class);
        $object2 = $this->container->resolve(TestInterface::class);
        $object1->setValue(2);
        $this->assertEquals(2, $object1->getValue());
        $this->assertEquals(2, $object2->getValue());
    }    

    public function testResolutionException()
    {
        $this->expectException(ResolutionException::class);
        $this->container->resolve('UnboundClass');
    }

    public function testAbstractResolutionException()
    {
        $this->expectException(ResolutionException::class);
        $this->container->resolve(AbstractClass::class);
    }

    public function testMixedConstructorNulls()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->resolve(\ntentan\panie\tests\classes\MixedConstructor::class);
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertNull($object->getString());
        $this->assertNull($object->getNumber());
    }

    public function testMixedConstructor()
    {
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->resolve(
            \ntentan\panie\tests\classes\MixedConstructor::class,
            ['string' => 'It is a string', 'number' => 2000]
        );
        $this->assertInstanceOf(TestClass::class, $object->getInterface());
        $this->assertEquals('It is a string', $object->getString());
        $this->assertEquals(2000, $object->getNumber());
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
        $object = $this->container->resolve(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $object);
    }

    public function testSetupSingleton()
    {
        $this->container->setup([TestInterface::class=>[TestClass::class, 'singleton' => true]]);
        $object1 = $this->container->resolve(TestInterface::class);
        $object2 = $this->container->resolve(TestInterface::class);
        $object1->setValue(2);
        $this->assertEquals(2, $object1->getValue());
        $this->assertEquals(2, $object2->getValue());
    }   
    
    public function testEmptyResolves()
    {
        $this->expectException(ResolutionException::class);
        $this->container->resolve(Unknown::class);
    }  
    
    public function testUnboundClass()
    {
        $object = $this->container->resolve(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $object);
    }
    
    public function testFactories()
    {
        $this->container->bind(TestInterface::class)->to(function($container){
            $this->assertInstanceOf(Container::class, $container);
            return new TestClass();
        });
        $object = $this->container->resolve(TestInterface::class);
        $this->assertInstanceOf(TestClass::class, $object);        
    }

    public function testCalls()
    {
        $this->container->bind('setters')->to(SettersAndProperties::class)->call('setTest')->call('setOther', ['other' => 'yay!']);
        $this->container->bind(TestInterface::class)->to(TestClass::class);
        $object = $this->container->resolve('setters');
        $this->assertInstanceOf(TestClass::class, $object->getTest());
        $this->assertEquals('yay!', $object->getOther());
    }

    public function testCallsWithStringHints()
    {
        $this->container->bind('setters')
            ->to(SettersAndProperties::class)
            ->call('setTest', ['test' => TestClass::class]);
        $object = $this->container->resolve('setters');
        $this->assertInstanceOf(TestClass::class, $object->getTest());
    }

    public function testOverwrite()
    {
        $this->container->bind('some_service')->to(SettersAndProperties::class);
        $this->assertInstanceOf(SettersAndProperties::class, $this->container->resolve('some_service'));
        $this->container->bind('some_service')->to(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $this->container->resolve('some_service'));
    }

    public function testArraySetup()
    {
        $this->container->setup([
            'setters' => [SettersAndProperties::class, 'calls' => ['setTest', 'setOther' => ['other' => 'yay!']]],
            TestInterface::class => TestClass::class
        ]);
        $object = $this->container->resolve('setters');
        $this->assertInstanceOf(TestClass::class, $object->getTest());
        $this->assertEquals('yay!', $object->getOther());
    }
}
