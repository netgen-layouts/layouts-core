<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\Parameter;

class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::__construct
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getName
     */
    public function testGetName()
    {
        $parameter = new Parameter('Parameter');

        self::assertEquals('Parameter', $parameter->getName());
    }
}
