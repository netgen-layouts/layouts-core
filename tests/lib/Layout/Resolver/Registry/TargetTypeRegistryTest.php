<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\TestCase;

final class TargetTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->targetType = new TargetType1('value');

        $this->registry = new TargetTypeRegistry([$this->targetType]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetTypes
     */
    public function testGetTargetTypes(): void
    {
        self::assertSame(['target1' => $this->targetType], $this->registry->getTargetTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     */
    public function testGetTargetType(): void
    {
        self::assertSame($this->targetType, $this->registry->getTargetType('target1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     */
    public function testGetTargetTypeThrowsTargetTypeException(): void
    {
        $this->expectException(TargetTypeException::class);
        $this->expectExceptionMessage('Target type "other" does not exist.');

        $this->registry->getTargetType('other');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetType(): void
    {
        self::assertTrue($this->registry->hasTargetType('target1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetTypeWithNoTargetType(): void
    {
        self::assertFalse($this->registry->hasTargetType('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getIterator
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
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('target1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->targetType, $this->registry['target1']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['target1'] = $this->targetType;
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['target1']);
    }
}
