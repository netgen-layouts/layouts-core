<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TargetTypeRegistry::class)]
final class TargetTypeRegistryTest extends TestCase
{
    private TargetType1 $targetType;

    private TargetTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->targetType = new TargetType1(42);

        $this->registry = new TargetTypeRegistry([$this->targetType]);
    }

    public function testGetTargetTypes(): void
    {
        self::assertSame(['target1' => $this->targetType], $this->registry->getTargetTypes());
    }

    public function testGetTargetType(): void
    {
        self::assertSame($this->targetType, $this->registry->getTargetType('target1'));
    }

    public function testGetTargetTypeThrowsTargetTypeException(): void
    {
        $this->expectException(TargetTypeException::class);
        $this->expectExceptionMessage('Target type "other" does not exist.');

        $this->registry->getTargetType('other');
    }

    public function testHasTargetType(): void
    {
        self::assertTrue($this->registry->hasTargetType('target1'));
    }

    public function testHasTargetTypeWithNoTargetType(): void
    {
        self::assertFalse($this->registry->hasTargetType('other'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $targetTypes = [];
        foreach ($this->registry as $identifier => $targetType) {
            $targetTypes[$identifier] = $targetType;
        }

        self::assertSame($this->registry->getTargetTypes(), $targetTypes);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('target1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->targetType, $this->registry['target1']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['target1'] = $this->targetType;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['target1']);
    }
}
