<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\Registry;

use ArrayIterator;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    private $valueType1;

    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    private $valueType2;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->valueType1 = ValueType::fromArray(['isEnabled' => true]);
        $this->valueType2 = ValueType::fromArray(['isEnabled' => false]);

        $this->registry = new ValueTypeRegistry(
            [
                'value1' => $this->valueType1,
                'value2' => $this->valueType2,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueTypes
     */
    public function testGetValueTypes(): void
    {
        self::assertSame(
            [
                'value1' => $this->valueType1,
                'value2' => $this->valueType2,
            ],
            $this->registry->getValueTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueTypes
     */
    public function testGetEnabledValueTypes(): void
    {
        self::assertSame(
            [
                'value1' => $this->valueType1,
            ],
            $this->registry->getValueTypes(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueType
     */
    public function testGetValueType(): void
    {
        self::assertSame($this->valueType1, $this->registry->getValueType('value1'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueType
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "other_value" does not exist.
     */
    public function testGetValueTypeThrowsInvalidArgumentException(): void
    {
        $this->registry->getValueType('other_value');
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::hasValueType
     */
    public function testHasValueType(): void
    {
        self::assertTrue($this->registry->hasValueType('value1'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::hasValueType
     */
    public function testHasValueTypeWithNoValueType(): void
    {
        self::assertFalse($this->registry->hasValueType('other_value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $valueTypes = [];
        foreach ($this->registry as $identifier => $valueType) {
            $valueTypes[$identifier] = $valueType;
        }

        self::assertSame($this->registry->getValueTypes(), $valueTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('value1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->valueType1, $this->registry['value1']);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['value1'] = $this->valueType1;
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['value1']);
    }
}
