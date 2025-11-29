<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueTypeRegistry::class)]
final class ValueTypeRegistryTest extends TestCase
{
    private ValueType $valueType1;

    private ValueType $valueType2;

    private ValueTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->valueType1 = ValueType::fromArray(['isEnabled' => true]);
        $this->valueType2 = ValueType::fromArray(['isEnabled' => false]);

        $this->registry = new ValueTypeRegistry(
            [
                'value1' => $this->valueType1,
                'value2' => $this->valueType2,
            ],
        );
    }

    public function testGetValueTypes(): void
    {
        self::assertSame(
            [
                'value1' => $this->valueType1,
                'value2' => $this->valueType2,
            ],
            $this->registry->getValueTypes(),
        );
    }

    public function testGetEnabledValueTypes(): void
    {
        self::assertSame(
            [
                'value1' => $this->valueType1,
            ],
            $this->registry->getValueTypes(true),
        );
    }

    public function testGetValueType(): void
    {
        self::assertSame($this->valueType1, $this->registry->getValueType('value1'));
    }

    public function testGetValueTypeThrowsInvalidArgumentException(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value type "other_value" does not exist.');

        $this->registry->getValueType('other_value');
    }

    public function testHasValueType(): void
    {
        self::assertTrue($this->registry->hasValueType('value1'));
    }

    public function testHasValueTypeWithNoValueType(): void
    {
        self::assertFalse($this->registry->hasValueType('other_value'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());
        self::assertSame($this->registry->getValueTypes(), [...$this->registry]);
    }

    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('value1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->valueType1, $this->registry['value1']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['value1'] = $this->valueType1;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['value1']);
    }
}
