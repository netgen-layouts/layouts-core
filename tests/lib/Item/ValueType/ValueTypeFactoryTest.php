<?php

declare(strict_types=1);

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
            [
                'name' => 'Value type',
                'enabled' => false,
            ]
        );

        $this->assertEquals(
            new ValueType(
                [
                    'identifier' => 'value',
                    'isEnabled' => false,
                    'name' => 'Value type',
                ]
            ),
            $valueType
        );
    }
}
