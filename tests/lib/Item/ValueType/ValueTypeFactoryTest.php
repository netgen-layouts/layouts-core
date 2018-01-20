<?php

namespace Netgen\BlockManager\Tests\Item\ValueType;

use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Item\ValueType\ValueTypeFactory;
use PHPUnit\Framework\TestCase;

final class ValueTypeFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueTypeFactory::buildValueType
     */
    public function testBuildValueType()
    {
        $valueType = ValueTypeFactory::buildValueType(
            'value',
            array(
                'name' => 'Value type',
                'enabled' => false,
            )
        );

        $this->assertEquals(
            new ValueType(
                array(
                    'identifier' => 'value',
                    'isEnabled' => false,
                    'name' => 'Value type',
                )
            ),
            $valueType
        );
    }
}
