<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\TestCase;

final class TargetTypeRegistryTest extends TestCase
{
    private TargetType1 $targetType;

    private TargetTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->targetType = new TargetType1(42);

        $this->registry = new TargetTypeRegistry([$this->targetType]);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::__construct
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::getTargetTypes
     */
    public function testGetTargetTypes(): void
    {
        self::assertSame(['target1' => $this->targetType], $this->registry->getTargetTypes());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     */
    public function testGetTargetType(): void
    {
        self::assertSame($this->targetType, $this->registry->getTargetType('target1'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     */
    public function testGetTargetTypeThrowsTargetTypeException(): void
    {
        $this->expectException(TargetTypeException::class);
        $this->expectExceptionMessage('Target type "other" does not exist.');

        $this->registry->getTargetType('other');
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetType(): void
    {
        self::assertTrue($this->registry->hasTargetType('target1'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetTypeWithNoTargetType(): void
    {
        self::assertFalse($this->registry->hasTargetType('other'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $targetTypes = [];
        foreach ($this->registry as $identifier => $targetType) {
            $targetTypes[$identifier] = $targetType;
        }

        self::assertSame($this->registry->getTargetTypes(), $targetTypes);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('target1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->targetType, $this->registry['target1']);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['target1'] = $this->targetType;
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['target1']);
    }
}
