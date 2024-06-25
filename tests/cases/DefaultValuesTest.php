<?php
namespace ntentan\panie\tests\cases;

use ntentan\panie\tests\classes\DefaultValues;
use PHPUnit\Framework\TestCase;
use ntentan\panie\Container;

/**
 * Description of DefaultValuesTest
 *
 * @author ekow
 */
class DefaultValuesTest extends TestCase
{
    private Container $container;

    public function setup() : void
    {
        $this->container = new Container();
        $this->container->setup([
            TestInterface::class => TestClass::class
        ]);        
    }
    
    public function testDefaultConstructors()
    {
        $defaultValues = $this->container->get(DefaultValues::class);
        $this->assertEquals("set by default", $defaultValues->getStringValue());
    }
}
