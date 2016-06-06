<?php

namespace Netgen\BlockManager\Tests\Configuration\Registry;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;

class LayoutTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    protected $layoutType;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new LayoutTypeRegistry();

        $this->layoutType = new LayoutType(
            'layout_type',
            true,
            'Layout type',
            array()
        );

        $this->registry->addLayoutType($this->layoutType);
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry::addLayoutType
     * @covers \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry::getLayoutTypes
     */
    public function testAddLayoutType()
    {
        self::assertEquals(array('layout_type' => $this->layoutType), $this->registry->getLayoutTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutType()
    {
        self::assertTrue($this->registry->hasLayoutType('layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry::hasLayoutType
     */
    public function testHasLayoutTypeWithNoLayoutType()
    {
        self::assertFalse($this->registry->hasLayoutType('other_layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry::getLayoutType
     */
    public function testGetLayoutType()
    {
        self::assertEquals($this->layoutType, $this->registry->getLayoutType('layout_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry::getLayoutType
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testGetLayoutTypeThrowsNotFoundException()
    {
        $this->registry->getLayoutType('other_layout_type');
    }
}
