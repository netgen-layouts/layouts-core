<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\ValueType;

use Netgen\Layouts\Item\ValueType\ValueType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueType::class)]
final class ValueTypeTest extends TestCase
{
    private ValueType $valueType;

    protected function setUp(): void
    {
        $this->valueType = ValueType::fromArray(
            [
                'identifier' => 'value',
                'isEnabled' => false,
                'name' => 'Value type',
                'supportsManualItems' => true,
            ],
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('value', $this->valueType->identifier);
    }

    public function testIsEnabled(): void
    {
        self::assertFalse($this->valueType->isEnabled);
    }

    public function testGetName(): void
    {
        self::assertSame('Value type', $this->valueType->name);
    }

    public function testSupportsManualItems(): void
    {
        self::assertTrue($this->valueType->supportsManualItems);
    }
}
