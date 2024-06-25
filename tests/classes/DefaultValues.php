<?php
namespace ntentan\panie\tests\classes;

class DefaultValues 
{

//    private TestInterface $interfaces;
    private string $stringValue;
    private int $numberValue = 240142;
    
    public function __construct(string $defaulted = "set by default") 
    {
//        $this->interfaces = $interface;
        $this->stringValue = $defaulted;
    }
    
    public function getInterface(): TestInterface
    {
        return $this->interfaces;
    }
    
    public function getNumberValue(): int
    {
        return $this->numberValue;
    }
    
    public function getStringValue(): string
    {
        return $this->stringValue;
    }
}
