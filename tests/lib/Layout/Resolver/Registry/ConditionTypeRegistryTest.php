<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\TestCase;

final class ConditionTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->conditionType = new ConditionType1();

        $this->registry = new ConditionTypeRegistry($this->conditionType);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionTypes
     */
    public function testGetConditionTypes(): void
    {
        self::assertSame(['condition1' => $this->conditionType], $this->registry->getConditionTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     */
    public function testGetConditionType(): void
    {
        self::assertSame($this->conditionType, $this->registry->getConditionType('condition1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     * @expectedException \Netgen\BlockManager\Exception\Layout\ConditionTypeException
     * @expectedExceptionMessage Condition type "other" does not exist.
     */
    public function testGetConditionTypeThrowsConditionTypeException(): void
    {
        $this->registry->getConditionType('other');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionType(): void
    {
        self::assertTrue($this->registry->hasConditionType('condition1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionTypeWithNoConditionType(): void
    {
        self::assertFalse($this->registry->hasConditionType('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $conditionTypes = [];
        foreach ($this->registry as $identifier => $conditionType) {
            $conditionTypes[$identifier] = $conditionType;
        }

        self::assertSame($this->registry->getConditionTypes(), $conditionTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('condition1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->conditionType, $this->registry['condition1']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['condition1'] = $this->conditionType;
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['condition1']);
    }
}
