<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Tests\Stubs\Parameter;
use PHPUnit_Framework_TestCase;

class ParameterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::__construct
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getIdentifier
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getName
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getAttributes
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter::getDefaultValue
     */
    public function testSetProperties()
    {
        $parameter = new Parameter(
            'parameter',
            'Parameter',
            array(
                'some_attribute' => 'some_value',
                'some_other_attribute' => 'some_other_value',
            ),
            'default'
        );

        self::assertEquals('parameter', $parameter->getIdentifier());
        self::assertEquals('Parameter', $parameter->getName());
        self::assertEquals(
            array(
                'some_attribute' => 'some_value',
                'some_other_attribute' => 'some_other_value',
            ),
            $parameter->getAttributes()
        );
        self::assertEquals('default', $parameter->getDefaultValue());
    }
}
