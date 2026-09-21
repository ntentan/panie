<?php
namespace ntentan\panie;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class ConstructWith
{
    public mixed $value;
    public string $argument;

    public function __construct(string $argument, mixed $value)
    {
        $this->value = $value;
        $this->argument = $argument;
    }
}
