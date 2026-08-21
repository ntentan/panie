<?php
namespace ntentan\panie\tests\classes;

class Variags
{
    private string $firstValue;
    private string $secondValue;
    private string $thirdValue;

    public function __construct(...$values)
    {
    }
}
