<?php

namespace Netgen\BlockManager\Tests\Layout\Registry;

use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

class LayoutTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    protected $layoutType;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new LayoutTypeRegistry();

        $this->layoutType = new LayoutType(array('identifier' => 'layout_type'));

        $this->registry->addLayoutType($this->layoutType);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::addLayoutType
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testAddLayoutType()
    {
        $this->assertEquals(array('layout_type' => $this->layoutType), $this->registry->getLayoutTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutType()
    {
        $this->assertTrue($this->registry->hasLayoutType('layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutTypeWithNoLayoutType()
    {
        $this->assertFalse($this->registry->hasLayoutType('other_layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutType
     */
    public function testGetLayoutType()
    {
        $this->assertEquals($this->layoutType, $this->registry->getLayoutType('layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Layout type with "other_layout_type" identifier does not exist.
     */
    public function testGetLayoutTypeThrowsInvalidArgumentException()
    {
        $this->registry->getLayoutType('other_layout_type');
    }
}
