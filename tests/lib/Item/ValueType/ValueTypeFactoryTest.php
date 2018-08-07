<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\ValueType;

use Netgen\BlockManager\Item\ValueType\ValueTypeFactory;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class ValueTypeFactoryTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueTypeFactory::buildValueType
     */
    public function testBuildValueType(): void
    {
        $valueType = ValueTypeFactory::buildValueType(
            'value',
            [
                'name' => 'Value type',
                'enabled' => false,
            ]
        );

        self::assertSame(
            [
                'identifier' => 'value',
                'isEnabled' => false,
                'name' => 'Value type',
            ],
            $this->exportObject($valueType)
        );
    }
}
