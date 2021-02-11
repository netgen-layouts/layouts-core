<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\TestCase;

final class ConditionTypeRegistryTest extends TestCase
{
    private ConditionType1 $conditionType;

    private ConditionTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->conditionType = new ConditionType1();

        $this->registry = new ConditionTypeRegistry([$this->conditionType]);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::__construct
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionTypes
     */
    public function testGetConditionTypes(): void
    {
        self::assertSame(['condition1' => $this->conditionType], $this->registry->getConditionTypes());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     */
    public function testGetConditionType(): void
    {
        self::assertSame($this->conditionType, $this->registry->getConditionType('condition1'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     */
    public function testGetConditionTypeThrowsConditionTypeException(): void
    {
        $this->expectException(ConditionTypeException::class);
        $this->expectExceptionMessage('Condition type "other" does not exist.');

        $this->registry->getConditionType('other');
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionType(): void
    {
        self::assertTrue($this->registry->hasConditionType('condition1'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionTypeWithNoConditionType(): void
    {
        self::assertFalse($this->registry->hasConditionType('other'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::getIterator
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
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('condition1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->conditionType, $this->registry['condition1']);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['condition1'] = $this->conditionType;
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['condition1']);
    }
}
