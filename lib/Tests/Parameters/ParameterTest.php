<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     */
    public function testGetName()
    {
        $parameter = new Parameter('Parameter');

        self::assertEquals('Parameter', $parameter->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getName
     */
    public function testGetIsRequired()
    {
        $parameter = new Parameter('Parameter', true);

        self::assertEquals(true, $parameter->isRequired());
    }
}
