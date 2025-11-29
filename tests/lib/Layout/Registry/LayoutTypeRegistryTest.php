<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Layout\Type\LayoutType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutTypeRegistry::class)]
final class LayoutTypeRegistryTest extends TestCase
{
    private LayoutType $layoutType1;

    private LayoutType $layoutType2;

    private LayoutTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->layoutType1 = LayoutType::fromArray(['identifier' => 'layout_type1', 'isEnabled' => true]);
        $this->layoutType2 = LayoutType::fromArray(['identifier' => 'layout_type2', 'isEnabled' => false]);

        $this->registry = new LayoutTypeRegistry(
            [
                'layout_type1' => $this->layoutType1,
                'layout_type2' => $this->layoutType2,
            ],
        );
    }

    public function testGetLayoutTypes(): void
    {
        self::assertSame(
            [
                'layout_type1' => $this->layoutType1,
                'layout_type2' => $this->layoutType2,
            ],
            $this->registry->getLayoutTypes(),
        );
    }

    public function testGetEnabledLayoutTypes(): void
    {
        self::assertSame(
            [
                'layout_type1' => $this->layoutType1,
            ],
            $this->registry->getLayoutTypes(true),
        );
    }

    public function testHasLayoutType(): void
    {
        self::assertTrue($this->registry->hasLayoutType('layout_type1'));
    }

    public function testHasLayoutTypeWithNoLayoutType(): void
    {
        self::assertFalse($this->registry->hasLayoutType('other_layout_type'));
    }

    public function testGetLayoutType(): void
    {
        self::assertSame($this->layoutType1, $this->registry->getLayoutType('layout_type1'));
    }

    public function testGetLayoutTypeThrowsLayoutTypeException(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Layout type with "other_layout_type" identifier does not exist.');

        $this->registry->getLayoutType('other_layout_type');
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());
        self::assertSame($this->registry->getLayoutTypes(), [...$this->registry]);
    }

    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('layout_type1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->layoutType1, $this->registry['layout_type1']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['layout_type1'] = $this->layoutType1;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['layout_type1']);
    }
}
