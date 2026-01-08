<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConditionTypeRegistry::class)]
final class ConditionTypeRegistryTest extends TestCase
{
    private ConditionType1 $conditionType;

    private ConditionTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->conditionType = new ConditionType1();

        $this->registry = new ConditionTypeRegistry([$this->conditionType]);
    }

    public function testGetConditionTypes(): void
    {
        self::assertSame(['condition1' => $this->conditionType], $this->registry->getConditionTypes());
    }

    public function testGetConditionType(): void
    {
        self::assertSame($this->conditionType, $this->registry->getConditionType('condition1'));
    }

    public function testGetConditionTypeThrowsConditionTypeException(): void
    {
        $this->expectException(ConditionTypeException::class);
        $this->expectExceptionMessage('Condition type "other" does not exist.');

        $this->registry->getConditionType('other');
    }

    public function testHasConditionType(): void
    {
        self::assertTrue($this->registry->hasConditionType('condition1'));
    }

    public function testHasConditionTypeWithNoConditionType(): void
    {
        self::assertFalse($this->registry->hasConditionType('other'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());
        self::assertSame($this->registry->getConditionTypes(), [...$this->registry]);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('condition1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->conditionType, $this->registry['condition1']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['condition1'] = $this->conditionType;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['condition1']);
    }
}
