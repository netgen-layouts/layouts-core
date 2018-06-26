<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Registry;

use ArrayIterator;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

final class LayoutTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    private $layoutType1;

    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    private $layoutType2;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->layoutType1 = new LayoutType(['identifier' => 'layout_type1', 'isEnabled' => true]);
        $this->layoutType2 = new LayoutType(['identifier' => 'layout_type2', 'isEnabled' => false]);

        $this->registry = new LayoutTypeRegistry(
            [
                'layout_type1' => $this->layoutType1,
                'layout_type2' => $this->layoutType2,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::__construct
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testGetLayoutTypes(): void
    {
        $this->assertSame(
            [
                'layout_type1' => $this->layoutType1,
                'layout_type2' => $this->layoutType2,
            ],
            $this->registry->getLayoutTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testGetEnabledLayoutTypes(): void
    {
        $this->assertSame(
            [
                'layout_type1' => $this->layoutType1,
            ],
            $this->registry->getLayoutTypes(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutType(): void
    {
        $this->assertTrue($this->registry->hasLayoutType('layout_type1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutTypeWithNoLayoutType(): void
    {
        $this->assertFalse($this->registry->hasLayoutType('other_layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutType
     */
    public function testGetLayoutType(): void
    {
        $this->assertSame($this->layoutType1, $this->registry->getLayoutType('layout_type1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Layout\LayoutTypeException
     * @expectedExceptionMessage Layout type with "other_layout_type" identifier does not exist.
     */
    public function testGetLayoutTypeThrowsLayoutTypeException(): void
    {
        $this->registry->getLayoutType('other_layout_type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $layoutTypes = [];
        foreach ($this->registry as $identifier => $layoutType) {
            $layoutTypes[$identifier] = $layoutType;
        }

        $this->assertSame($this->registry->getLayoutTypes(), $layoutTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::count
     */
    public function testCount(): void
    {
        $this->assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('layout_type1', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertSame($this->layoutType1, $this->registry['layout_type1']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['layout_type1'] = $this->layoutType1;
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['layout_type1']);
    }
}
