<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterType;
use PHPUnit\Framework\TestCase;

final class ParameterTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType
     */
    private $parameterType;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->parameterType = new ParameterType();

        $this->registry = new ParameterTypeRegistry(
            $this->parameterType
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypes
     */
    public function testGetParameterTypes(): void
    {
        $this->assertSame(['type' => $this->parameterType], $this->registry->getParameterTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::hasParameterType
     */
    public function testHasParameterType(): void
    {
        $this->assertTrue($this->registry->hasParameterType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::hasParameterType
     */
    public function testHasParameterTypeWithNoParameterType(): void
    {
        $this->assertFalse($this->registry->hasParameterType('other_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterType
     */
    public function testGetParameterType(): void
    {
        $this->assertSame($this->parameterType, $this->registry->getParameterType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterType
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter type with "other_type" identifier does not exist.
     */
    public function testGetParameterTypeThrowsParameterTypeException(): void
    {
        $this->registry->getParameterType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypeByClass
     */
    public function testGetParameterTypeByClass(): void
    {
        $this->assertSame($this->parameterType, $this->registry->getParameterTypeByClass(ParameterType::class));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypeByClass
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Parameter type with class "SomeClass" does not exist.
     */
    public function testGetParameterTypeByClassThrowsParameterTypeException(): void
    {
        $this->registry->getParameterTypeByClass('SomeClass');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $parameterTypes = [];
        foreach ($this->registry as $identifier => $parameterType) {
            $parameterTypes[$identifier] = $parameterType;
        }

        $this->assertSame($this->registry->getParameterTypes(), $parameterTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::count
     */
    public function testCount(): void
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('type', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertSame($this->parameterType, $this->registry['type']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['type'] = $this->parameterType;
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['type']);
    }
}
