<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterTypeRegistry::class)]
final class ParameterTypeRegistryTest extends TestCase
{
    private ParameterType $parameterType;

    private ParameterTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->parameterType = new ParameterType();

        $this->registry = new ParameterTypeRegistry([$this->parameterType]);
    }

    public function testGetParameterTypes(): void
    {
        self::assertSame(['type' => $this->parameterType], $this->registry->getParameterTypes());
    }

    public function testHasParameterType(): void
    {
        self::assertTrue($this->registry->hasParameterType('type'));
    }

    public function testHasParameterTypeWithNoParameterType(): void
    {
        self::assertFalse($this->registry->hasParameterType('other_type'));
    }

    public function testGetParameterType(): void
    {
        self::assertSame($this->parameterType, $this->registry->getParameterType('type'));
    }

    public function testGetParameterTypeThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter type with "other_type" identifier does not exist.');

        $this->registry->getParameterType('other_type');
    }

    public function testGetParameterTypeByClass(): void
    {
        self::assertSame($this->parameterType, $this->registry->getParameterTypeByClass(ParameterType::class));
    }

    public function testGetParameterTypeByClassThrowsParameterTypeException(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Parameter type with class "SomeClass" does not exist.');

        $this->registry->getParameterTypeByClass('SomeClass');
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $parameterTypes = [];
        foreach ($this->registry as $identifier => $parameterType) {
            $parameterTypes[$identifier] = $parameterType;
        }

        self::assertSame($this->registry->getParameterTypes(), $parameterTypes);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('type', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->parameterType, $this->registry['type']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['type'] = $this->parameterType;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['type']);
    }
}
