<?php

namespace ntentan\panie\tests\classes;


class SettersAndProperties
{
    private $test;
    private $other;

    public function setTest(TestInterface $test)
    {
        $this->test = $test;
    }

    public function setOther($other)
    {
        $this->other = $other;
    }

    public function setMixed(TestInterface $test, $other)
    {
        $this->test = $test;
        $this->other = $other;
    }

    public function getTest()
    {
        return $this->test;
    }

    public function getOther()
    {
        return $this->other;
    }
}