<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\Parameter;

class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::__construct
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getDefaultValue
     */
    public function testGetDefaultValue()
    {
        $parameter = new Parameter('default');

        self::assertEquals('default', $parameter->getDefaultValue());
    }
}
