<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\ValueType;

use Netgen\Layouts\Item\ValueType\ValueTypeFactory;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class ValueTypeFactoryTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\Layouts\Item\ValueType\ValueTypeFactory::buildValueType
     */
    public function testBuildValueType(): void
    {
        $valueType = ValueTypeFactory::buildValueType(
            'value',
            [
                'name' => 'Value type',
                'enabled' => false,
                'manual_items' => true,
            ],
        );

        self::assertSame(
            [
                'identifier' => 'value',
                'isEnabled' => false,
                'name' => 'Value type',
                'supportsManualItems' => true,
            ],
            $this->exportObject($valueType),
        );
    }
}
