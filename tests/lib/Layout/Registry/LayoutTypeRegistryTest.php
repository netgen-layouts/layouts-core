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
    protected $layoutType1;

    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    protected $layoutType2;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new LayoutTypeRegistry();

        $this->layoutType1 = new LayoutType(array('identifier' => 'layout_type1', 'isEnabled' => true));
        $this->layoutType2 = new LayoutType(array('identifier' => 'layout_type2', 'isEnabled' => false));

        $this->registry->addLayoutType('layout_type1', $this->layoutType1);
        $this->registry->addLayoutType('layout_type2', $this->layoutType2);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::addLayoutType
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testGetLayoutTypes()
    {
        $this->assertEquals(
            array(
                'layout_type1' => $this->layoutType1,
                'layout_type2' => $this->layoutType2,
            ),
            $this->registry->getLayoutTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::addLayoutType
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testGetEnabledLayoutTypes()
    {
        $this->assertEquals(
            array(
                'layout_type1' => $this->layoutType1,
            ),
            $this->registry->getLayoutTypes(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutType()
    {
        $this->assertTrue($this->registry->hasLayoutType('layout_type1'));
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
        $this->assertEquals($this->layoutType1, $this->registry->getLayoutType('layout_type1'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry::getLayoutType
     * @expectedException \Netgen\BlockManager\Exception\Layout\LayoutTypeException
     * @expectedExceptionMessage Layout type with "other_layout_type" identifier does not exist.
     */
    public function testGetLayoutTypeThrowsLayoutTypeException()
    {
        $this->registry->getLayoutType('other_layout_type');
    }
}
