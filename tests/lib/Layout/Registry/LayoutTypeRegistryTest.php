<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Registry;

use ArrayIterator;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

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

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::__construct
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
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

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testGetEnabledLayoutTypes(): void
    {
        self::assertSame(
            [
                'layout_type1' => $this->layoutType1,
            ],
            $this->registry->getLayoutTypes(true),
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutType(): void
    {
        self::assertTrue($this->registry->hasLayoutType('layout_type1'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutTypeWithNoLayoutType(): void
    {
        self::assertFalse($this->registry->hasLayoutType('other_layout_type'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::getLayoutType
     */
    public function testGetLayoutType(): void
    {
        self::assertSame($this->layoutType1, $this->registry->getLayoutType('layout_type1'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::getLayoutType
     */
    public function testGetLayoutTypeThrowsLayoutTypeException(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Layout type with "other_layout_type" identifier does not exist.');

        $this->registry->getLayoutType('other_layout_type');
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $layoutTypes = [];
        foreach ($this->registry as $identifier => $layoutType) {
            $layoutTypes[$identifier] = $layoutType;
        }

        self::assertSame($this->registry->getLayoutTypes(), $layoutTypes);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('layout_type1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->layoutType1, $this->registry['layout_type1']);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['layout_type1'] = $this->layoutType1;
    }

    /**
     * @covers \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['layout_type1']);
    }
}
