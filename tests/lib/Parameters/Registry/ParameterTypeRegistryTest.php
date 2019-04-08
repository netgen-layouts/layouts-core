<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
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

        $this->registry = new ParameterTypeRegistry([$this->parameterType]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypes
     */
    public function testGetParameterTypes(): void
    {
        self::assertSame(['type' => $this->parameterType], $this->registry->getParameterTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::hasParameterType
     */
    public function testHasParameterType(): void
    {
        self::assertTrue($this->registry->hasParameterType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::hasParameterType
     */
    public function testHasParameterTypeWithNoParameterType(): void
    {
        self::assertFalse($this->registry->hasParameterType('other_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterType
     */
    public function testGetParameterType(): void
    {
        self::assertSame($this->parameterType, $this->registry->getParameterType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterType
     */
    public function testGetParameterTypeThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter type with "other_type" identifier does not exist.');

        $this->registry->getParameterType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypeByClass
     */
    public function testGetParameterTypeByClass(): void
    {
        self::assertSame($this->parameterType, $this->registry->getParameterTypeByClass(ParameterType::class));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypeByClass
     */
    public function testGetParameterTypeByClassThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter type with class "SomeClass" does not exist.');

        $this->registry->getParameterTypeByClass('SomeClass');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $parameterTypes = [];
        foreach ($this->registry as $identifier => $parameterType) {
            $parameterTypes[$identifier] = $parameterType;
        }

        self::assertSame($this->registry->getParameterTypes(), $parameterTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('type', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->parameterType, $this->registry['type']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['type'] = $this->parameterType;
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['type']);
    }
}
