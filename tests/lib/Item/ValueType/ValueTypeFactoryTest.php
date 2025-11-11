<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\ValueType;

use Netgen\Layouts\Item\ValueType\ValueTypeFactory;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueTypeFactory::class)]
final class ValueTypeFactoryTest extends TestCase
{
    use ExportObjectTrait;

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
