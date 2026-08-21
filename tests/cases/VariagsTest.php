<?php
namespace ntentan\panie\tests\cases;

use ntentan\panie\Container;
use PHPUnit\Framework\TestCase;
use ntentan\panie\tests\classes\Variags;

class VariagsTest extends TestCase
{

    private Container $container;

    public function setup() : void
    {
        $this->container = new Container();
    }

    public function testVariags()
    {
        $variags = $this->container->get(Variags::class);
        $this->assertInstanceOf(Variags::class, $variags);
    }
}
