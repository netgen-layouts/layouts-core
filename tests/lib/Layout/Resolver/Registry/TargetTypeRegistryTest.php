<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
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
        $this->registry = new TargetTypeRegistry();

        $this->targetType = new TargetType('type', 'value');
        $this->registry->addTargetType($this->targetType);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::addTargetType
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetTypes
     */
    public function testGetTargetTypes(): void
    {
        $this->assertSame(['type' => $this->targetType], $this->registry->getTargetTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     */
    public function testGetTargetType(): void
    {
        $this->assertSame($this->targetType, $this->registry->getTargetType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     * @expectedException \Netgen\BlockManager\Exception\Layout\TargetTypeException
     * @expectedExceptionMessage Target type "other_type" does not exist.
     */
    public function testGetTargetTypeThrowsTargetTypeException(): void
    {
        $this->registry->getTargetType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetType(): void
    {
        $this->assertTrue($this->registry->hasTargetType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetTypeWithNoTargetType(): void
    {
        $this->assertFalse($this->registry->hasTargetType('other_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $targetTypes = [];
        foreach ($this->registry as $identifier => $targetType) {
            $targetTypes[$identifier] = $targetType;
        }

        $this->assertSame($this->registry->getTargetTypes(), $targetTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::count
     */
    public function testCount(): void
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('type', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertSame($this->targetType, $this->registry['type']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['type'] = $this->targetType;
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['type']);
    }
}
